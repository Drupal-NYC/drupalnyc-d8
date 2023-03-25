<?php

namespace DrupalNycRobo;

use GuzzleHttp\Client;
use \Psr\Http\Message\ResponseInterface;
use Robo\Result;
use Robo\Task\BaseTask;

/**
 * A task for interacting with GitHub.
 *
 * Borrows extensively from \Robo\Task\Development\GitHub but uses Guzzle for
 * interacting with GitHub.
 *
 * Robo uses AnnotatedCommand, so only command parameters should have an
 * [at]param comment on command methods:
 *
 * phpcs:disable Drupal.Commenting.FunctionComment.ParamMissingDefinition
 */
class GitHubApi extends BaseTask {

  const GITHUB_URL = 'https://api.github.com';

  /**
   * @var string
   */
  protected $repo;

  /**
   * @var string
   */
  protected $owner;

  /**
   * @var string
   */
  protected $accessToken;

  /**
   * The REST api uri.
   *
   * @var string
   */
  protected $uri = '';

  /**
   * A description of the current task.
   *
   * @var string
   */
  protected $taskInfo;

  /**
   * A description of the current task.
   *
   * @var string
   */
  protected $method = 'POST';

  /**
   * An array of parameters.
   *
   * @var array
   */
  protected $parameters = [];

  /**
   * @param string $repo
   *
   * @return $this
   */
  public function repo($repo)
  {
    $this->repo = $repo;
    return $this;
  }

  /**
   * @param string $owner
   *
   * @return $this
   */
  public function owner($owner)
  {
    $this->owner = $owner;
    return $this;
  }

  /**
   * @param string $uri
   *
   * @return $this
   */
  public function uri($uri)
  {
    [$this->owner, $this->repo] = explode('/', $uri);
    return $this;
  }

  /**
   * @return string
   */
  protected function getUri()
  {
    return $this->owner . '/' . $this->repo;
  }

  /**
   * @param string $token
   *
   * @return $this
   */
  public function accessToken($token)
  {
    $this->accessToken = $token;
    return $this;
  }

  /**
   * Check for existing tag.
   *
   * @var string $tag
   *   The tag or partial tag name.
   */
  public function existingTag(string $tag) {
    $this->taskInfo = "Checking for tags matching: $tag";
    $this->uri = "git/matching-refs/tags/$tag";
    $this->method = 'GET';
    return $this;
  }

  /**
   * Create a tag at a SHA.
   *
   * @var string $tag
   *   The tag.
   * @var string $sha
   *   The SHA on which to set the tag.
   */
  public function createTag(string $tag, string $sha) {
    $this->taskInfo = "Creating tag: $tag";
    $this->uri = 'git/refs';
    $this->parameters = [
      'ref' => "refs/tags/$tag",
      'sha' => $sha,
    ];
    $this->method = 'POST';
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function run(): Result {
    $this->printTaskInfo($this->taskInfo);
    $this->startTimer();
    [$code, $data] = $this->sendRequest();
    $this->startTimer();

    return new Result(
      $this,
      in_array($code, [200, 201]) ? 0 : 1,
      $data->message ?? '',
      ['response' => $data, 'time' => $this->getExecutionTime()]
    );
  }

  /**
   * Utility method to configure Guzzle.
   *
   * @return \GuzzleHttp\Client
   */
  protected function getClient() {
    $options = [
      'verify' => TRUE,
      'timeout' => 30,
      'headers' => [
        'User-Agent' => 'Robo' . \GuzzleHttp\default_user_agent(),
        'Authorization' => 'Bearer ' . $this->accessToken
      ],
      'proxy' => [
        'http' => NULL,
        'https' => NULL,
        'no' => [],
      ],
    ];
    return new Client($options);
  }

  /**
   * Use Guzzle to send the request.
   *
   * @return array
   *   The response code and response data wrapped in an array.
   *
   * @throws \Robo\Exception\TaskException
   */
  protected function sendRequest() {
    $url = sprintf('%s/repos/%s/%s', self::GITHUB_URL, $this->getUri(), $this->uri);
    $this->printTaskInfo('{method} {url}', ['method' => $this->method, 'url' => $url]);
    $client = $this->getClient();
    $response = NULL;
    $code = NULL;
    $data = NULL;
    switch ($this->method) {
      case 'GET':
        $response = $client->get($url);
        break;

      case 'POST':
        $response = $client->post($url, [
          'json' => $this->parameters,
        ]);
    }
    if ($response instanceof ResponseInterface) {
      $code = $response->getStatusCode();
      $data = json_decode((string) $response->getBody(), TRUE);
    }
    return [$code, $data];
  }

}
