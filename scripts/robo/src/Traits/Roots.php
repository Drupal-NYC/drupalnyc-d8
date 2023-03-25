<?php

namespace DrupalNycRobo\Traits;

use DrupalFinder\DrupalFinder;

/**
 * Provides methods to easily obtain common file paths.
 */
trait Roots {

  /**
   * Drupal root path.
   *
   * @var string
   */
  protected $drupalRoot;

  /**
   * Project root path.
   *
   * @var string
   */
  protected $projectRoot;

  /**
   * Is ddev running?
   *
   * @var bool
   */
  private $ddevPresent;

  /**
   * Get the path to the Drupal root directory.
   *
   * @return string
   *   The drupal root path.
   */
  public function getDrupalRoot() {
    if (empty($this->drupalRoot)) {
      $this->setRoots();
    }
    return $this->drupalRoot;
  }

  /**
   * Get the path to the project root directory.
   *
   * @return string
   *   The project root path.
   */
  public function getProjectRoot() {
    if (empty($this->projectRoot)) {
      $this->setRoots();
    }
    return $this->projectRoot;
  }

  /**
   * Gets the path to the drush executable.
   *
   * @return string
   *   The command path.
   */
  protected function getDrushPath() {
    return $this->getProjectRoot() . '/vendor/bin/drush';
  }

  /**
   * Utility function to insure root paths are set.
   */
  protected function setRoots() {
    // Values are set here so one empty is a sufficient flag.
    if (empty($this->drupalRoot)) {
      $drupalFinder = new DrupalFinder();
      $drupalFinder->locateRoot(getcwd());
      $this->drupalRoot = $drupalFinder->getDrupalRoot();
      $this->projectRoot = $drupalFinder->getComposerRoot();
    }
  }

  /**
   * Helper function to detect ddev.
   *
   * @return bool
   *   Is ddev running?
   */
  protected function useDdev(): bool {
    if (is_null($this->ddevPresent)) {
      $root = $this->getProjectRoot();
      $this->ddevPresent = file_exists("$root/.ddev/.ddev-docker-compose-base.yaml");
    }
    return $this->ddevPresent;
  }

}
