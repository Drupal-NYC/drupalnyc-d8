<?php

/**
 * @file
 * Provides an additional config form for theme settings.
 */

use Drupal\Core\Form\FormStateInterface;

// Set theme name to use in the key values.
$theme_name = \Drupal::theme()->getActiveTheme()->getName();


//  https://www.drupal.org/docs/8/theming-drupal-8/creating-advanced-theme-settings
/**
 * Implements hook_form_system_theme_settings_alter().
 */
function bones_form_system_theme_settings_alter(&$form, &$form_state) {

  // https://medium.com/@sarahcodes/using-custom-theme-settings-in-templates-in-drupal-8-925391b8cff1
  $form['bones_options'] = array(
    '#type'          => 'details',
    '#title'         => t('Bones Theme Options'),
    '#weight'        => -999,
    '#open'          => TRUE,
  );

  $form['bones_options']['bones_fixed_header'] = array(
      '#type'           => 'checkbox',
      '#title'          => t('Use the Bones sticky header'),
      '#description'    => t('A fixed header that shrinks in size when scrolling'),
      '#default_value'  => theme_get_setting('bones_fixed_header'),
  );

  $form['bones_options']['bones_search_box'] = array(
      '#type'           => 'checkbox',
      '#title'          => t('Use the Bones search box'),
      '#description'    => t('Show a Search icon that shows the textbox on click'),
      '#default_value'  => theme_get_setting('bones_search_box'),
  );

  $form['bones_options']['bones_mobile_menu'] = array(
      '#type'           => 'checkbox',
      '#title'          => t('Use the Bones mobile menu'),
      '#description'    => t('The mobile navigation region will be toggled with a menu button'),
      '#default_value'  => theme_get_setting('bones_mobile_menu'),
  );

  $form['bones_options']['bones_info'] = array(
      '#markup' => '<div class="messages messages--warning">Flush the cache after making any changes to these settings. <a href="../../config/development/performance">Click here to flush the cache</a></div>'
    );

}
