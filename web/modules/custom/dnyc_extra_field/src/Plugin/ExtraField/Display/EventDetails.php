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
    if ($node->hasField('schema_event_schedule')
      && !($node->get('schema_event_schedule')->isEmpty())
    ) {
      $startDate = $node->get('schema_event_schedule')->view('embed');
    }
    if ($node->hasField('schema_address')
      && !($node->get('schema_address')->isEmpty())) {
      $location = $node->get('schema_address')->view('embed');
    }
    if (
      $useDescription instanceof BooleanItem
      && $useDescription->getValue()['value']
      && $node->hasField('schema_description')
      && !($node->get('schema_description')->isEmpty())
    ) {
      $description = $node->get('schema_description')->view('embed');
    }

    $details = [
      '#theme' => 'dnyc_event_details',
      '#start_date' => $startDate ?? NULL,
      '#location' => $location ?? NULL,
      '#description' => $description ?? NULL,
    ];

    return $details;
  }

}
