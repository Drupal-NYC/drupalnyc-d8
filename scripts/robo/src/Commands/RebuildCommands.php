<?php

namespace DrupalNycRobo\Commands;

use DrupalNycRobo\Traits\GetResult;
use DrupalNycRobo\Traits\Roots;
use Robo\Common\TimeKeeper;
use Robo\Tasks;
use Robo\Contract\VerbosityThresholdInterface;
use Robo\Symfony\ConsoleIO;
use Symfony\Component\Finder\Finder;

/**
 * Robo commands for rebuild local environments.
 *
 * Robo uses AnnotatedCommand, so only command parameters should have an
 * [at]param comment on command methods:
 *
 * phpcs:disable Drupal.Commenting.FunctionComment.ParamMissingDefinition
 *
 * @package DrupalNycRobo\Commands
 */
class RebuildCommands extends Tasks {

  use Roots;
  use GetResult;

  /**
   * Rebuild the local site from a remote server with granular options.
   *
   * @param string $environment
   *   The environment suffix for the drush alias.
   *
   * @command rebuild:site
   *
   * @option no-code Skip composer and yarn installs.
   * @option no-db Skip db retrieval from the remote server.
   * @option no-update Skip database updates and configuration import.
   *
   * @throws \Exception
   */
  public function rebuildSite(ConsoleIO $io, $environment = 'dev', array $options = [
    'no-code' => FALSE,
    'no-db' => FALSE,
    'no-update' => FALSE,
  ]) {
    // Run the task with options. All methods exit with an exception on failure:
    if (!$options['no-code']) {
      $this->getUpdatedPackages($io);
      // $this->getRebuiltTheme($io);
    }
    if (!$options['no-db']) {
      $this->getRemoteDump($io, $environment);
      $this->getImport($io);
    }
    if (!$options['no-update']) {
      $this->getUpdate($io);
    }
    $io->success("Local site rebuilt from $environment");
  }

  /**
   * Rebuild the local codebase with composer and yarn and then run updates.
   *
   * @command rebuild:code
   *
   * @option no-update Skip database updates and configuration import.
   *
   * @throws \Exception
   */
  public function rebuildCode(ConsoleIO $io, array $options = [
    'no-update' => FALSE,
  ]) {
    // Run the task with options. All methods exit with an exception on failure:
    $this->getUpdatedPackages($io);
    // $this->getRebuiltTheme($io);
    if (!$options['no-update']) {
      $this->getUpdate($io);
    }
  }

  /**
   * Pull down and load a remote database and then run updates.
   *
   * @param string $environment
   *   The environment suffix for the drush alias.
   *
   * @command rebuild:db
   *
   * @option no-import Skip database import.
   * @option no-update Skip database updates and configuration import.
   * @option build-image Package the database into a docker image after import.
   *
   * @throws \Exception
   */
  public function rebuildDatabase(ConsoleIO $io, $environment = 'test', array $options = [
    'no-import' => FALSE,
    'no-update' => FALSE,
  ]) {
    if ($options['no-import']) {
      $io->text('--no-import will get a fresh copy of the target db and not import it nor run updates.');
      $options['no-update'] = TRUE;
    }
    // Run the task with options. All methods exit with an exception on failure:
    $this->getRemoteDump($io, $environment);
    if (!$options['no-import']) {
      $this->getImport($io);
      if ($options['build-image']) {
        $this->setDockerImage($io);
      }
    }
    if (!$options['no-update']) {
      $this->getUpdate($io);
    }
  }

  /* ####################
   *
   * Internal utility methods.
   *
   * These functions modularize the tasks needed by the commands methods.
   *
   * ####################
   */

  /**
   * Dump the remote database from the Acquia host.
   *
   * @param \Robo\Symfony\ConsoleIO $io
   *   Input-output service.
   * @param string $target
   *   The full drush alias of the target remote.
   *
   * @throws \Exception
   */
  protected function getRemoteDump(ConsoleIO $io, $target) {
    $root = $this->getProjectRoot();
    $destination = $this->getDestination();
    $io->text('Dumping and retrieving remote database.');
    $dumpRemote = $this->collectionBuilder();
    $dumpRemote->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_NORMAL);
    // Add a request for a fresh dump to the task collection.
    $dumpRemote->addTask(
      $this->taskExec("terminus backup:get $target --element=db --to=$root/database.sql.gz")
        ->printMetadata(FALSE)
        ->printOutput(FALSE)
    );
    $dumpRemote->addTask(
      $this->taskExec("gunzip -c $root/database.sql.gz > $destination")
        ->printMetadata(FALSE)
        ->printOutput(FALSE)
    );
    $dumpRemote->addTask(
      $this->taskFilesystemStack()->remove("$root/database.sql.gz")
    );
    $result = $this->getResult($dumpRemote);
    if ($result->wasSuccessful()) {
      $io->text('Remote database retrieved.');
    }
    else {
      $io->error('Failed to retrieve the remote database.');
      $result->stopOnFail();
    }
  }

  /**
   * Import the dump into the docker managed database.
   *
   * @param \Robo\Symfony\ConsoleIO $io
   *   Input-output service.
   *
   * @throws \Exception
   */
  protected function getImport(ConsoleIO $io) {
    // We invoke sanitize in more than once context.
    $sanitize = 'sqlsan -y ' . self::getSanitizeParmeters('dnyc');
    $io->text('Loading remote dump to local database.');
    $loadRemote = $this->collectionBuilder();
    $loadRemote->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_VERBOSE);
    $source = $this->getDestination();
    $loadRemote->addTask(
      $this->taskExec("ddev import-db --src=$source")
        ->printMetadata(FALSE)
        ->printOutput(FALSE)
    );
    $loadRemote->addTask(
      $this->taskExec("ddev drush $sanitize")
        ->printMetadata(FALSE)
        ->printOutput(FALSE)
    );
    $io->text('Reloading the remote database...');
    $loadResult = $this->getResult($loadRemote);
    if ($loadResult->wasSuccessful()) {
      $time = TimeKeeper::formatDuration($loadResult->getExecutionTime());
      $io->text("Remote database loaded in $time.");
    }
    else {
      $io->error('Remote database failed to load.');
      $loadResult->stopOnFail();
    }
  }

  /**
   * Run update hooks and import local config.
   *
   * @param \Robo\Symfony\ConsoleIO $io
   *   Input-output service.
   *
   * @throws \Exception
   */
  protected function getUpdate(ConsoleIO $io) {
    $io->text('Running database updates and importing config.');
    $updates = $this->collectionBuilder();
    $updates->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_NORMAL);
    // In this set of tasks, the command calls are the same,
    // but the path differs.
    $commandPath = $this->getDrushPath();
    if ($this->useDdev()) {
      $commandPath = "ddev drush";
    }
    $updates->addTask(
      $this->taskExec("$commandPath -y deploy")
        ->printMetadata(FALSE)
        ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_NORMAL)
    );
    $updates->addTask(
      $this->taskExec("$commandPath -y msk-update custom")
        ->printMetadata(FALSE)
        ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_NORMAL)
    );
    $updateResult = $this->getResult($updates);
    if ($updateResult->wasSuccessful()) {
      $io->text('Updates complete.');
    }
    else {
      $io->error('Database updates and/or config imports failed to load.');
      $updateResult->stopOnFail();
    }
  }

  /**
   * Run composer install and yarn install.
   *
   * @param \Robo\Symfony\ConsoleIO $io
   *   Input-output service.
   *
   * @throws \Exception
   */
  protected function getUpdatedPackages(ConsoleIO $io) {
    $root = $this->getProjectRoot();
    $io->text('Updating code base with Composer...');
    $composerInstall = $this->taskComposerInstall()
      ->noInteraction()
      ->ansi()
      ->dir($root)
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG);
    $composerResult = $this->getResult($composerInstall);
    if ($composerResult->wasSuccessful()) {
      $io->text('Code base updated.');
    }
    else {
      $io->error('`composer install` failed.');
      $composerResult->stopOnFail();
    }
    $io->text('Updating theme tools and upstream with Yarn...');
    $yarnInstall = $this->taskExec('yarn install')
      ->dir($root)
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG);
    $yarnResult = $this->getResult($yarnInstall);
    if ($yarnResult->wasSuccessful()) {
      $io->text('Theme tools and upstream updated.');
    }
    else {
      $io->error('`yarn install` failed.');
      $yarnResult->stopOnFail();
    }
  }

  /**
   * Rebuild the theme.
   *
   * @param \Robo\Symfony\ConsoleIO $io
   *   Input-output service.
   */
  protected function getRebuiltTheme(ConsoleIO $io) {
    $root = $this->getProjectRoot();
    $io->text('Building the theme...');
    $themeCompile = $this->taskExec('yarn build')
      ->dir($root)
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG);
    $themeResult = $this->getResult($themeCompile);
    if ($themeResult->wasSuccessful()) {
      $io->text('Theme compiled');
    }
    else {
      $io->error('The theme failed to compile.');
      $themeResult->stopOnFail();
    }
  }

  /**
   * Helper function to determine db dump directory.
   *
   * @return string
   *   The file path to the db dump directory.
   */
  protected function getDestination(): string {
    $root = $this->getProjectRoot();
    return "$root/db_backup/target.sql";
  }

  /**
   * Static helper method for configuring sanitizing.
   *
   * @param string $password
   *   The common password to set.
   *
   * @return string
   *   The configured command parameters.
   */
  public static function getSanitizeParmeters(string $password = ''): string {
    $password_parameter = empty($password) ? '' : '--sanitize-password=' . $password;
    return $password_parameter . ' --yes --sanitize-email=user+%uid@sanitized.mskcc.org --allowlist-fields=field_ldap_employeeid,field_ldap_memberof,field_ldap_cn,field_ldap_dn';
  }

}
