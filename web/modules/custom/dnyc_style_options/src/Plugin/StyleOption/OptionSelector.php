<?php

declare(strict_types=1);

namespace Drupal\dnyc_style_options\Plugin\StyleOption;

use Drupal\Core\Form\FormStateInterface;
use Drupal\style_options\Plugin\StyleOptionPluginBase;

/**
 * Define the media position option plugin.
 *
 * @StyleOption(
 *   id = "option_selector",
 *   label = @Translation("Option selector")
 * )
 */
class OptionSelector extends StyleOptionPluginBase {

  /**
   * {@inheritDoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    if ($this->hasConfiguration('options')) {
      // We don't display this style option if options are undefined.
      $options = [];
      foreach ($this->getConfiguration('options') as $option) {
        $options[$option['value']] = $option['label'];
      }
      $plugin_id = $this->getPluginId();
      $form[$plugin_id] = [
        '#type' => 'select',
        '#title' => $this->getLabel(),
        '#default_value' => $this->getValue($plugin_id) ?? $this->getDefaultValue(),
        '#description' => $this->getConfiguration('description'),
        '#options' => $options,
      ];
    }
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function build(array $build) {
    $plugin_id = $this->getPluginId();
    $value = $this->getValue($plugin_id) ?? NULL;
    if (!empty($value)) {
      $build['#' . $plugin_id] = $value;
    }
    return $build;
  }

}
