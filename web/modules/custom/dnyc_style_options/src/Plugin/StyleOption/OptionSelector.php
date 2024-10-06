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
      $option_id = $this->getConfiguration('option_id');
      $form[$option_id] = [
        '#type' => 'select',
        '#title' => $this->getLabel(),
        '#default_value' => $this->getValue($option_id) ?? $this->getDefaultValue(),
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
    $option_id = $this->getConfiguration('option_id');
    $value = $this->getValue($option_id) ?? NULL;
    if (!empty($value)) {
      $build['#' . $option_id] = $value;
    }
    return $build;
  }

}
