<?php

namespace Drupal\dnyc_extra_field\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\BooleanItem;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;
use Drupal\node\Entity\Node;
use Drupal\text\Plugin\Field\FieldType\TextItem;

/**
 * Hero title extra_field.
 *
 * @ExtraFieldDisplay(
 *   id = "dnyc_event_details",
 *   label = @Translation("Event Details"),
 *   description = @Translation("An extra field to display event details within
 *   the layout."),
 *   bundles = {
 *     "paragraph.event_details"
 *   }
 * )
 */
class EventDetails extends ExtraFieldDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    /** @var \Drupal\paragraphs\Entity\Paragraph $entity */
    if (!$entity->hasField('field_include_event_description')) {
      return [];
    }
    $useDescription = $entity->get('field_include_event_description')->first();
    $node = $entity->getParentEntity();
    if (!($node instanceof Node)) {
      return [];
    }
    if ($node->hasField('schema_start_date')
      && !($node->get('schema_start_date')->isEmpty())
    ) {
      $startDate = $node->get('schema_start_date')->view(
        [
          'type' => 'datetime_default',
          'label' => 'inline',
          'settings' => [
            'timezone_override' => '',
            'format_type' => 'medium',
          ],
        ]
      );
    }
    if ($node->hasField('schema_end_date')
      && !($node->get('schema_end_date')->isEmpty())
    ) {
      $endDate = $node->get('schema_end_date')->view(
        [
          'type' => 'datetime_default',
          'label' => 'inline',
          'settings' => [
            'timezone_override' => '',
            'format_type' => 'medium',
          ],
        ]
      );
    }
    if ($node->hasField('schema_address')
      && !($node->get('schema_address')->isEmpty())) {
      $location = $node->get('schema_address')->view(
        [
          'type' => 'address_default',
          'label' => 'inline',
        ]
      );
    }
    if (
      $useDescription instanceof BooleanItem
      && $useDescription->getValue()['value']
      && $node->hasField('schema_description')
      && !($node->get('schema_description')->isEmpty())
    ) {
      $description = $node->get('schema_description')->view(
        [
          'type' => 'text_default',
          'label' => 'inline',
        ]
      );
    }

    $details = [
      '#theme' => 'dnyc_event_details',
      '#start_date' => $startDate ?? NULL,
      '#end_date' => $endDate ?? NULL,
      '#location' => $location ?? NULL,
      '#description' => $description ?? NULL,
    ];

    return $details;
  }

}
