<?php

namespace Drupal\dnyc_date_augmenters\Plugin\DateAugmenter;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Url;
use Drupal\Core\Utility\Token;
use Drupal\date_augmenter\DateAugmenter\DateAugmenterPluginBase;
use Drupal\date_augmenter\Plugin\PluginFormTrait;
use Drupal\pathauto\AliasCleanerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Date Augmenter plugin to inject ti.to links.
 *
 * @DateAugmenter(
 *   id = "dnyc_tito_link",
 *   label = @Translation("Tito Link"),
 *   description = @Translation("Adds tito links to an event"),
 *   weight = 0
 * )
 */
class TitoLink extends DateAugmenterPluginBase implements PluginFormInterface {

  use PluginFormTrait;

  /**
   * Injected alias cleaner service.
   */
  protected AliasCleanerInterface $aliasCleaner;

  /**
   * Injected token service.
   */
  protected Token $token;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->aliasCleaner = $container->get('pathauto.alias_cleaner');
    $instance->token = $container->get('token');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'link_text' => 'Register Now!',
      'class' => '',
      'past_events' => FALSE,
    ];
  }

  /**
   * Create configuration fields for the plugin form, or injected directly.
   *
   * @param array $form
   *   The form array.
   * @param array|null $settings
   *   The setting to use as defaults.
   * @param mixed $field_definition
   *   Expected parameter to define the field being modified.
   *
   * @return array
   *   The updated form array.
   */
  public function configurationFields(array $form, ?array $settings, $field_definition = NULL) {
    if (empty($settings)) {
      $settings = $this->defaultConfiguration();
    }

    $form['link_text'] = [
      '#title' => $this->t('Link text'),
      '#type' => 'textfield',
      '#default_value' => $settings['link_text'],
      '#description' => $this->t('The text to use in the registration link. You can use static text or tokens.'),
      '#required' => TRUE,
    ];

    $form['class'] = [
      '#title' => $this->t('Class'),
      '#description' => $this->t('A CSS class to apply to the link. If using multiple classes, separate them by spaces.'),
      '#type' => 'textfield',
      '#default_value' => $settings['class'],
    ];

    $form['past_events'] = [
      '#title' => $this->t('Show link for past events?'),
      '#type' => 'checkbox',
      '#default_value' => $settings['past_events'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $this->configurationFields($form, $this->configuration);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function augmentOutput(array &$output, DrupalDateTime $start, ?DrupalDateTime $end = NULL, array $options = []) {
    $config = $options['settings'] ?? $this->getConfiguration();
    $end_fallback = $end ?? $start;
    $now = new DrupalDateTIme();
    if ($end_fallback < $now && !$config['past_events']) {
      return;
    }
    $base = 'https://ti.to/drupalnyc/';
    $path = '';
    $text = $config['link_text'];
    $entity = $options['entity'] ?? NULL;
    if ($entity instanceof ContentEntityInterface) {
      if ($entity->hasField('field_tito_link')) {
        $values = $entity->get('field_tito_link')->first()->getValue();
        $value = (bool) $values['value'] ?? FALSE;
        if ($value === FALSE) {
          // Do not add the link.
          return;
        }
      }
      $path .= $entity->label() . '-';
      $token_data = [
        $entity->getEntityTypeId() => $entity,
      ];
      $text = $this->token->replace($text, $token_data);
    }
    $path .= $start->format('Y-m-j');
    $path = $this->aliasCleaner->cleanString($path);
    $uri = $base . $path;
    $url = Url::fromUri($uri);
    $output['link'] = [
      '#title' => Xss::filter($text),
      '#type' => 'link',
      '#url' => $url,
      '#prefix' => ' ',
      '#suffix' => ' ',
    ];
    // Parse and sanitize provided classes.
    if ($config['class']) {
      $classes = explode(' ', $config['class']);
      foreach ($classes as $index => $class) {
        $classes[$index] = Html::getClass($class);
      }
      $output['link']['#attributes']['class'] = $classes;
    }
  }

}
