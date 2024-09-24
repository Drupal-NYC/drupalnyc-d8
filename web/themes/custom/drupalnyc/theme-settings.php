<?php

/**
 * @file
 * Functions to support drupalnyc theme settings.
 */

use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter() for system_theme_settings.
 */
function drupalnyc_form_system_theme_settings_alter(&$form, FormStateInterface $form_state) {
  $form['drupalnyc_settings'] = [
    '#type' => 'details',
    '#title' => t('Settings'),
    '#open' => TRUE,
    '#weight' => -10,
  ];
  $form['drupalnyc_settings']['display_back_to_top'] = [
    '#type' => 'checkbox',
    '#title' => t('Enable back to top button'),
    '#default_value' => theme_get_setting('display_back_to_top'),
    '#description' => t('Display a back to top button on the site.'),
  ];
}
