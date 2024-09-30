<?php

namespace Drupal\dnyc_extra_field\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;
use Drupal\node\Entity\Node;
use Drupal\text\Plugin\Field\FieldType\TextItem;

/**
 * Hero title extra_field.
 *
 * @ExtraFieldDisplay(
 *   id = "dnyc_hero_title",
 *   label = @Translation("Hero title"),
 *   description = @Translation("An extra field to display schema_image as a hero."),
 *   bundles = {
 *     "paragraph.hero"
 *   }
 * )
 */
class HeroHeadlineTitle extends ExtraFieldDisplayBase {

  /**
   * @inheritDoc
   */
  public function view(ContentEntityInterface $entity) {
    /** @var \Drupal\paragraphs\Entity\Paragraph $entity */
    if (!$entity->hasField('field_heading')) {
      return [];
    }
    $heading = $entity->get('field_heading')->first();
    $node = $entity->getParentEntity();
    if (!($node instanceof Node)) {
      return [];
    }
    if (!($heading instanceof TextItem) || $heading->isEmpty()) {
      $label = $node->label();
      if ($label instanceof TranslatableMarkup) {
        return [
          '#markup' => $label,
        ];
      }
      return [
        '#plain_text' => $label,
      ];
    }
    return [
      '#type' => 'processed_text',
      '#text' => $heading->value,
      '#format' => $heading->format,
      '#langcode' => $heading->getLangcode(),
    ];
  }

}
