<?php

/**
 * @file
 * This file holds common settings for all non-Acquia servers.
 *
 * It is only included outside Acquia servers.
 */

// This is the default - same as settings.local.php
// Get the path to the parent of web.
$dir = dirname(DRUPAL_ROOT);
$settings['config_sync_directory'] = $dir . '/config';
