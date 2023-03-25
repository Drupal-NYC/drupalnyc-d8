<?php

namespace DrupalNycRobo\Commands;

use DrupalNycRobo\GitHubApi;
use DrupalNycRobo\NewRelicApi;
use DrupalNycRobo\Traits\GetResult;
use DrupalNycRobo\Traits\Roots;
use Robo\Tasks;
use Robo\Result;
use Robo\Contract\VerbosityThresholdInterface;
use Robo\Symfony\ConsoleIO;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

/**
 * Robo commands that manage deployment.
 *
 * Robo uses AnnotatedCommand, so only command parameters should have an
 * [at]param comment on command methods:
 *
 * phpcs:disable Drupal.Commenting.FunctionComment.ParamMissingDefinition
 *
 * @package DrupalNycRobo\Commands
 */
class DeploymentCommands extends Tasks {
  use Roots;
  use GetResult;

  /**
   * A string to use in the action to create a remote.
   */
  const REMOTE_NAME = 'pantheon';

  const REMOTE_URL = 'ssh://codeserver.dev.948cdd82-38e2-4769-a8fb-d3e33ee4e161@codeserver.dev.948cdd82-38e2-4769-a8fb-d3e33ee4e161.drush.in:2222/~/repository.git';

  /**
   * Builds separate artifact and pushes to remote defined in REMOTE_URL.
   *
   * If the tag parameter is set, deploy:tag is called, otherwise deploy:branch.
   *
   * @throws \Exception
   *   Throws an exception if the deployment fails.
   *
   * @aliases deploy
   * @option branch The source branch name in the local repository.
   * @option tag A tag name to set on the deployment.
   * @option commit-msg The message to use for the deployment commit.
   * @option remote-branch The target branch name in the remote repository.
   * @option remote-url The url of the remote. Defaults to a stored constant.
   * @option github-token A GitHub API token.
   * @option github-repo A GitHub repo in the format `owner/repo_name`.
   * @option github-sha A commit SHA, usually provide by actions environment.
   * @option release-tag When present, calculate and deploy a release tag.
   */
  public function deployBuild(ConsoleIO $io, array $options = [
    'branch' => NULL,
    'tag' => NULL,
    'commit-msg' => NULL,
    'remote-branch' => NULL,
    'remote-url' => NULL,
    'github-token' => NULL,
    'github-repo' => NULL,
    'github-sha' => NULL,
    'release-tag' => FALSE,
  ]) {
    $this->setDeploymentOptions($options);
    if (!empty($options['tag']) || $options['release-tag']) {
      // If the release-tag option is set then create the tag.
      if ($options['release-tag']) {
        $this->setReleaseTag($io, $options);
      }
      $result = $this->getTagPush($io, $options);
    }
    else {
      $result = $this->getBranchPush($io, $options);
    }
    if ($result->wasSuccessful()) {
      $io->success('Deployment succeeded.');
    }
    else {
      $io->error('Deployment failed');
      $result->stopOnFail();
    }
  }

  /**
   * Run the deployment sequence for a site.
   *
   * This command is designed to be used with Acquia cloud hooks.
   *
   * @param string $site
   *   The site key used in the alias file. Example: 'mskcc'.
   * @param string $target
   *   The target environment key. Example: 'dev'.
   *
   * @command deploy:update
   *
   * @description Execute update tasks a remote site.
   *
   * @throws \Robo\Exception\TaskException
   * @throws \Exception
   */
  public function deployPostUpdate(ConsoleIO $io, string $site = 'self', string $target = '') {
    $alias = empty($target) ? $site : "$site.$target";
    $task = $this->taskExecStack()
      ->stopOnFail()
      ->exec($this->getDrushPath() . " @$alias deploy")
      ->exec($this->getDrushPath() . " @$alias msk-update custom");
    $result = $this->getResult($task);
    if ($result->wasSuccessful()) {
      $io->text("Updates deployed for $site.$target");
      return;
    }
    $io->error("Updates failed to deploy for $site.$target");
  }

  /**
   * Mark a deployment in New Relic.
   *
   * This command is designed to be used with Acquia cloud hooks.
   *
   * @param string $tag
   *   The tag value.
   * @param string $site
   *   The site key used in the alias file. Example: 'mskcc'.
   * @param string $target
   *   The target environment key. Example: 'dev'.
   *
   * @command deploy:new-relic
   */
  public function newRelic(ConsoleIO $io, $tag, string $site, string $target) {
    $credentialFinder = new Finder();
    $credentials = [];
    $credentialFinder->in("/mnt/gfs/$site.$target/nobackup")
      ->files()
      ->name('new_relic.json');
    if ($credentialFinder->hasResults()) {
      foreach ($credentialFinder as $file) {
        // There should only be 1!
        $credentialsJson = $file->getContents();
      }
      $credentials = json_decode($credentialsJson, TRUE);
    }
    if (!empty($credentials)) {
      /** @var \DrupalNycRobo\NewRelicApi $newRelic */
      $newRelic = $this->task(NewRelicApi::class);
      $newRelic->setApiKey($credentials['api_key'])
        ->setApplication($credentials['app_id'])
        ->markDeployment($tag);
      $result = $this->getResult($newRelic);
      if ($result->wasSuccessful()) {
        $io->text("Marked $tag deployment in New Relic");
      }
      else {
        $io->error('Failed to pushed to GitHub Containers.');
        $result->stopOnFail();
      }
    }
    else {
      $io->error('Failed to retrieve New Relic credentials.');
    }
  }

  /**
   * Builds separate artifact and pushes as a tag.
   *
   * @param \Robo\Symfony\ConsoleIO $io
   *   Injected input-output service.
   * @param array $options
   *   Options prepared in ::deployBuild.
   *
   * @return \Robo\Result
   *   The result of the final push.
   *
   * @throws \Exception
   *   Throws an exception if the tasks fail so that deployment will abort.
   */
  public function getTagPush(ConsoleIO $io, array $options): Result {
    if (empty($options['tag'])) {
      // A tag is required.
      throw new \UnexpectedValueException('A tag must be specified.');
    }
    $io->text('Deploying to tag ' . $options['tag']);
    $this->setDeploymentVersionControl($io, $options);
    $this->getSanitizedBuild($io);
    $this->setDeploymentCommit($io, $options);
    $this->setCleanMerge($io, $options);
    return $this->getPushResult($options);
  }

  /**
   * Builds separate artifact and pushes to remote as a branch.
   *
   * @param \Robo\Symfony\ConsoleIO $io
   *   Injected input-output service.
   * @param array $options
   *   Options prepared in ::deployBuild.
   *
   * @return \Robo\Result
   *   The result of the final push.
   *
   * @throws \Exception
   *   Throws an exception if the tasks fail so that deployment will abort.
   */
  public function getBranchPush(ConsoleIO $io, array $options): Result {
    $io->text('Deploying to branch ' . $options['remote-branch']);
    $this->setDeploymentVersionControl($io, $options);
    $this->getSanitizedBuild($io);
    $this->setDeploymentCommit($io, $options);
    $this->setCleanMerge($io, $options);
    return $this->getPushResult($options);
  }

  /**
   * All the methods that follow are protected helper methods.
   */

  /**
   * Insures that appropriate options are present.
   *
   * @param array $options
   *   Options passed from the command.
   */
  protected function setDeploymentOptions(array &$options): void {
    $options['remote-url'] = $options['remote-url'] ?? self::REMOTE_URL;
    $options['commit-id'] = $options['commit-id'] ?? substr(getenv('GITHUB_SHA'), 0, 7);
    // A local branch is required.
    if (empty($options['branch'])) {
      $github_ref_name = getenv('GITHUB_REF_NAME');
      if ($github_ref_name !== FALSE) {
        $branch = preg_replace('#[\s/^~:.\\\\]#', '-', $github_ref_name);
        $this->taskGitStack()
          ->stopOnFail()
          ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
          ->exec("branch $branch")
          ->run();
        $options['branch'] = $branch;
      }
      else {
        throw new \UnexpectedValueException('A branch must be specified.');
      }
    }
    $options['remote-branch'] = $options['remote-branch'] ?? $options['branch'];
    $options['deploy-branch'] = $options['remote-branch'] . '-deploy';
    // There should also be a commit-message.
    if (empty($options['commit-msg'])) {
      $options['commit-msg'] = 'Deployment built for DrupalNYC';
      if (!empty($options['commit-id'])) {
        $options['commit-msg'] .= ' GitHub commit: ' . $options['commit-id'];
      }
    }
  }

  /**
   * Calculates a tag value based on target branch.
   *
   * @param \Robo\Symfony\ConsoleIO $io
   *   Injected input-output service.
   * @param array $options
   *   Options passed from the command and refined ::setDeploymentOptions().
   *
   * @throws \Exception
   */
  protected function setReleaseTag(ConsoleIO $io, array &$options): void {
    $releaseCandidatePattern = '/^rc-\d{4}-\d{2}-\d{2}$/';
    $releasePattern = '/^release$/';
    $date = date('Y-m-d');
    $suffix = 0;
    if (preg_match($releaseCandidatePattern, $options['branch'])) {
      $stem = "RC-$date";
      $io->text('Release candidate detected for tag.');
    }
    elseif (preg_match($releasePattern, $options['branch'])) {
      $stem = $date;
      $io->text('Release detected for tag.');
    }
    else {
      throw new \UnexpectedValueException("The branch {$options['branch']} is not enabled for automated date tags.");
    }
    if (
      !empty($options['github-token'])
      && !empty($options['github-repo'])
      && !empty($options['github-sha'])
    ) {
      /** @var \DrupalNycRobo\GitHubApi $api */
      $api = $this->task(GitHubApi::class);
      $api->accessToken($options['github-token']);
      $api->uri($options['github-repo']);
      $existingTags = $this->getResult($api->existingTag($stem));
      if ($existingTags->wasSuccessful()) {
        $data = $existingTags->getData();
        $response = $data['response'] ?? [];
        $suffix = count($response);
      }
      $options['tag'] = "$stem.$suffix";
      $options['tag-remote'] = $options['tag'] . '-artifact';
      $io->text("Setting automated release tag as $stem.$suffix which will push as $stem.$suffix-artifact");
      $result = $this->getResult($api->createTag($options['tag'], $options['github-sha']));
      if ($result->wasSuccessful()) {
        $io->success("Created tag: {$options['tag']}");
      }
      else {
        $io->text('Failed to create release tag.');
        $result->stopOnFail();
      }
    }
    else {
      $io->error('Unable to set release tag.  GitHub options are not all present.');
    }
  }

  /**
   * Adds the deployment target as a remote.
   *
   * @param \Robo\Symfony\ConsoleIO $io
   *   Robo IO service.
   * @param array $options
   *   Options passed from the command.
   *
   * @throws \Robo\Exception\TaskException|\Exception
   *   Throws an exception if the tasks fail so that deployment will abort.
   */
  protected function setDeploymentVersionControl(ConsoleIO $io, array $options): void {
    $git_name = getenv('GIT_NAME') ? getenv('GIT_NAME') : 'Deployment';
    $git_mail = getenv('GIT_EMAIL') ? getenv('GIT_EMAIL') : 'deploy@example.com';
    $remote_url = $options['remote-url'];
    $remote_name = self::REMOTE_NAME;
    $io->text("Will push to git remote $remote_name at $remote_url");
    $result = $this->getResult(
      $this->taskExecStack()
        ->dir($this->getProjectRoot())
        ->stopOnFail()
        ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
        ->exec("git remote add $remote_name $remote_url")
        ->exec("git config --global user.email $git_mail")
        ->exec("git config --global user.name $git_name")
    );
    if ($result->wasSuccessful()) {
      $io->text('Git config set.');
    }
    else {
      $io->text('Git config failed to set.');
      $result->stopOnFail();
    }
    $shallowCheck = $this->getResult(
      $this->taskExec('git rev-parse --is-shallow-repository --no-revs HEAD')
        ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
        ->interactive(FALSE)
    );
    if (trim($shallowCheck->getMessage()) == 'true') {
      $io->text('"Unshallow" the local repo.');
      $unShallow = $this->taskExec('git fetch --unshallow');
      $result = $this->getResult($unShallow);
      if (!$result->wasSuccessful()) {
        throw new \Exception('Git repo did not unshallow.');
      }
      $io->text('Local repo "unshallowed"');
    }

  }

  /**
   * Removes files and directories that should not be deployed.
   *
   * Also replaces .gitignore with deployment specific versions.
   *
   * @param \Robo\Symfony\ConsoleIO $io
   *   Robo IO service.
   *
   * @throws \Exception
   *   Throws an exception if the files are unable to be modified.
   */
  protected function getSanitizedBuild(ConsoleIO $io): void {
    $io->text('Sanitizing build artifact...');
    $io->text('Finding .git subdirectories...');
    $excludedFiles = new Finder();
    $excludedFiles
      ->in([
        $this->getProjectRoot() . '/vendor',
        $this->getProjectRoot() . '/web',
      ])
      ->directories()
      ->ignoreDotFiles(FALSE)
      ->ignoreVCS(FALSE)
      ->name('/^\.git$/');
    $io->text($excludedFiles->count() . ' .git directories found');

    $io->text('Finding entries in exclude.yml');
    $excludeList = Yaml::parseFile($this->getProjectRoot() . '/.github/deploy/exclude.yml');
    foreach ($excludeList as $name) {
      $exclude = new Finder();
      $exclude->in($this->getProjectRoot())
        ->name($name);
      if (str_starts_with($name, '.')) {
        $exclude->ignoreDotFiles(FALSE);
      }
      if (str_ends_with($name, '/')) {
        $exclude->directories();
        $type = 'directories';
      }
      else {
        $exclude->files();
        $type = 'files';
      }
      $io->text($exclude->count() . " $name $type found.");
      $excludedFiles->append($exclude);
    }

    $taskFilesystemStack = $this->taskFilesystemStack();
    foreach ($excludedFiles->getIterator() as $item) {
      $taskFilesystemStack
        ->remove($item->getRealPath());
    }
    $collection = $this->collectionBuilder();
    $collection->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG);
    $taskFilesystemStack->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG);
    $collection->addTask($taskFilesystemStack);
    $collection->addTask(
      $this->taskFilesystemStack()
        ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
        ->copy($this->getProjectRoot() . '/.github/deploy/deploy-gitignore',
          $this->getProjectRoot() . '/.gitignore', TRUE)
    );
    $result = $this->getResult($collection);
    if ($result->wasSuccessful()) {
      $io->text('Build artifact sanitized.');
    }
    else {
      $io->text('Build artifact failed to sanitize');
      $result->stopOnFail();
    }
  }

  /**
   * Commits the result of the deployment build.
   *
   * @param array $options
   *   Options passed from the command.
   *
   * @throws \Exception
   *   Throws an exception if the git tasks fail so that deployment will abort.
   */
  protected function setDeploymentCommit(ConsoleIO $io, array $options): void {
    $gitJobs = $this->taskGitStack()
      ->stopOnFail()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->checkout($options['branch'])
      ->add('-A')
      ->commit($options['commit-msg']);
    $result = $this->getResult($gitJobs);
    if ($result->wasSuccessful()) {
      $io->text('Build successfully committed.');
    }
    else {
      $io->text('Build failed to commit');
      $result->stopOnFail();
    }
  }

  /**
   * Cleanly merges the current branch into the remote branch of the same name.
   *
   * Uses a simulated "theirs" strategy to prefer any changes or updates in
   * the current branch over that in the remote.
   *
   * @param \Robo\Symfony\ConsoleIO $io
   *   Robo IO service.
   * @param array $options
   *   Options passed from the command.
   *
   * @throws \Exception
   *   Throws an exception if the branch checkout or merge fails.
   *
   * @see https://stackoverflow.com/questions/173919/is-there-a-theirs-version-of-git-merge-s-ours/4969679#4969679
   */
  protected function setCleanMerge(ConsoleIO $io, array $options): void {
    // We need some simple variables for expansion in a string.
    $remote = self::REMOTE_NAME;
    $fetch = "fetch $remote " . $options['remote-branch'];
    $target = "$remote/" . $options['remote-branch'];
    $message = 'Merge to remote: ' . $options['commit-msg'];
    $local = $options['deploy-branch'];
    $io->text("Move to a new branch, $local, that tracks $target.");
    $gitJobs = $this->taskGitStack()
      ->stopOnFail()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec($fetch)
      ->checkout("-b $local --track $target");
    $result = $this->getResult($gitJobs);
    if (!$result->wasSuccessful()) {
      $io->text("Remote branch $target was not available.  Creating $local branch");
      // The remote branch may not exist.  Just make the target branch.
      $gitJobs = $this->taskGitStack()
        ->stopOnFail()
        ->checkout("-b $local");
      $result = $this->getResult($gitJobs);
      if (!$result->wasSuccessful()) {
        throw new \Exception('Unable to checkout or create a deployment branch.');
      }
    }
    // Now cleanly merge $options['branch'] into $options['deploy-branch']
    // with preference to $options['branch'] for any conflicts.
    $io->text('Merging changes into remote tracking branch');
    $merge = $this->taskGitStack()
      ->stopOnFail()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->exec('merge -s ours ' . $options['branch'] . " -m \"$message\"")
      ->exec('branch branch-temp')
      ->exec('reset --hard ' . $options['branch'])
      ->exec('reset --soft branch-temp')
      ->exec('commit --amend -C HEAD')
      ->exec('branch -D branch-temp');
    if ($options['tag-remote']) {
      $merge->tag($options['tag-remote']);
    }
    $result = $this->getResult($merge);
    if ($result->wasSuccessful()) {
      $io->text('Code is ready to push');
    }
    else {
      $io->text('Failed to merge deployment into the remote branch.');
      $result->stopOnFail();
    }
  }

  /**
   * Attempts to push the build and returns the final result.
   *
   * @param array $options
   *   Options passed from the command.
   *
   * @return \Robo\Result
   *   The final task result.
   *
   * @throws \Exception
   */
  protected function getPushResult(array $options): Result {
    $gitJobs = $this->taskGitStack()
      ->stopOnFail()
      ->setVerbosityThreshold(VerbosityThresholdInterface::VERBOSITY_DEBUG)
      ->push(self::REMOTE_NAME, $options['deploy-branch'] . ':' . $options['remote-branch']);
    if (!empty($options['tag-remote'])) {
      $gitJobs->push(self::REMOTE_NAME, $options['tag-remote']);
    }
    return $this->getResult($gitJobs);
  }

}
