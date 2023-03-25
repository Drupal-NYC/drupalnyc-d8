<?php

namespace DrupalNycRobo\Traits;

use Robo\Result;
use Robo\Contract\TaskInterface;

/**
 * Reusable method to ensure a Result object is returned.
 */
trait GetResult {

  /**
   * Ensure a Result is returned from ::run().
   *
   * @param \Robo\Contract\TaskInterface $task
   *   The task to run.
   *
   * @return \Robo\Result
   *   The result object.
   *
   * @throws \Exception
   */
  protected function getResult(TaskInterface $task) {
    $result = $task->run();
    return Result::ensureResult($task, $result);
  }

}
