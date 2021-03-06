<?php

/**
 * @file
 * Pantheon additional configuration file.
 */

// @codingStandardsIgnoreFile

if (isset($_ENV['PANTHEON_ENVIRONMENT']) && php_sapi_name() != 'cli') {
  // Redirect to https://$primary_domain in the Live environment.
  if ($_ENV['PANTHEON_ENVIRONMENT'] === 'live') {
    $primary_domain = 'www.drupalnyc.org';
  }
  else {
    // Redirect to HTTPS on every Pantheon environment.
    $primary_domain = $_SERVER['HTTP_HOST'];
  }
  if ($_SERVER['HTTP_HOST'] != $primary_domain) {

    // Name transaction "redirect" in New Relic: improved reporting (optional).
    if (extension_loaded('newrelic')) {
      newrelic_name_transaction("redirect");
    }

    header('HTTP/1.0 301 Moved Permanently');
    header('Location: https://' . $primary_domain . $_SERVER['REQUEST_URI']);
    exit();
  }
  // Drupal 8 Trusted Host Settings.
  if (isset($settings) && is_array($settings)) {
    $settings['trusted_host_patterns'] = ['^' . preg_quote($primary_domain) . '$'];
  }
}

if (isset($_ENV['PANTHEON_ENVIRONMENT'])) {

  switch ($_ENV['PANTHEON_ENVIRONMENT']) {
    case 'dev':
      break;

    case 'test':
      break;

    case 'live':
      if (PHP_SAPI !== 'cli') {
        $settings['config_readonly'] = TRUE;
        $settings['config_readonly_whitelist_patterns'] = [
          'core.menu.static_menu_link_overrides',
          'menu_ui.settings.yml',
          'system.menu.*',
        ];
      }
      $config['config_split.config_split.dev']['status'] = FALSE;
      $config['config_split.config_split.prod']['status'] = TRUE;
      ini_set('display_errors', '0');
      break;

  }
}
