<?php

/**
 * @file
 * Post update functions for Drupalnyc.
 */

/**
 * Enable new theme: stable9.
 */
function drupalnyc_post_update_install_stable9() {
  // Install new subtheme stable9.
  /** @var \Drupal\Core\Extension\ThemeInstallerInterface $theme_installer */
  $theme_installer = \Drupal::service('theme_installer');
  $theme_installer->install(['stable9']);
}

/**
 * Uninstall old themes: stable and classy.
 */
function drupalnyc_post_update_uninstall_stable_classy() {
  // Remove classy and stable.
  /**@var \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler */
  $theme_handler = \Drupal::service('theme_handler');
  /** @var \Drupal\Core\Extension\ThemeInstallerInterface $theme_installer */
  $theme_installer = \Drupal::service('theme_installer');

  foreach (['classy', 'stable'] as $theme) {
    if ($theme_handler->themeExists($theme)) {
      $theme_installer->uninstall([$theme]);
    }
  }
}
