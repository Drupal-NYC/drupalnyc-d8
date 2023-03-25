<?php

namespace DrupalNycRobo\Commands;

use DrupalNycRobo\Traits\GetResult;
use DrupalNycRobo\Traits\Roots;
use Robo\Symfony\ConsoleIO;
use Robo\Tasks;

/**
 * Commands to run phpunit tests.
 *
 * Robo uses AnnotatedCommand, so only command parameters should have an
 * [at]param comment on command methods:
 *
 * phpcs:disable Drupal.Commenting.FunctionComment.ParamMissingDefinition
 *
 * @package DrupalNycRobo\Commands
 */
class TestCommands extends Tasks {
  use Roots;
  use GetResult;

  /**
   * Run PHP Unit tests.
   *
   * @command tests:all
   *
   * @description A runner for phpunit in DrupalNyc custom code.
   *
   * @throws \Exception
   */
  public function unitTests(ConsoleIO $io) {
    $project = $this->getProjectRoot();
    $drupal = $this->getDrupalRoot();
    $io->text('PHPUnit Testing: modules/custom');
    $task = $this->taskPhpUnit("$project/vendor/bin/phpunit")
      ->group('DrupalNycPHPUnit')
      ->configFile("$drupal/core/phpunit.xml.dist")
      ->file("$drupal/modules/custom");
    $resultCustom = $this->getResult($task);
    $resultCustom->stopOnFail();
    $io->text('PHPUnit Testing: modules/sandbox');
    $task = $this->taskPhpUnit("$project/vendor/bin/phpunit")
      ->group('DrupalNycPHPUnit')
      ->configFile("$drupal/core/phpunit.xml.dist")
      ->file("$drupal/modules/sandbox");
    $resultSandbox = $this->getResult($task);
    $resultSandbox->stopOnFail();
    $io->text('PHPUnit Live Testing: modules/custom');
    $task = $this->taskPhpUnit("$project/vendor/bin/phpunit")
      ->group('DrupalNycLiveTest')
      ->configFile("$drupal/core/phpunit.xml.dist")
      ->file("$drupal/modules/custom");
    $resultLive = $this->getResult($task);
    $resultLive->stopOnFail();
    $io->text("Site passes all unit tests");
  }

  /**
   * Run PHP Unit tests.
   *
   * @param string $target
   *   The target directory or file as a path relative to the docroot directory.
   *
   * @command tests:single
   *
   * @description A runner for phpunit in DrupalNyc custom code.
   *
   * @throws \Exception
   */
  public function unitTest(ConsoleIO $io, string $target) {
    $project = $this->getProjectRoot();
    $drupal = $this->getDrupalRoot();
    $io->text("Running tests from: $target");
    $task = $this->taskPhpUnit("$project/vendor/bin/phpunit")
      ->configFile("$drupal/core/phpunit.xml.dist")
      ->file("$drupal/$target");
    $result = $this->getResult($task);
    $result->stopOnFail();
    $io->text("Tests in $target all pass.");
  }

}
