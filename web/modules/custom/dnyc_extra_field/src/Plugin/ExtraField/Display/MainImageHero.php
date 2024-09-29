<?php

namespace Drupal\dnyc_extra_field\Plugin\ExtraField\Display;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Hero image extra_field.
 *
 * @ExtraFieldDisplay(
 *   id = "dnyc_hero_image",
 *   label = @Translation("Hero image"),
 *   description = @Translation("An extra field to display schema_image as a hero."),
 *   bundles = {
 *     "paragraph.hero"
 *   }
 * )
 */
class MainImageHero extends ExtraFieldDisplayBase implements ContainerFactoryPluginInterface {

  protected EntityViewBuilderInterface $viewBuilder;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static(
      $configuration, $plugin_id, $plugin_definition,
    );
    $entityTypeManager = $container->get('entity_type.manager');
    $instance->viewBuilder = $entityTypeManager->getViewBuilder('media');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity) {
    /** @var \Drupal\paragraphs\Entity\Paragraph $entity */
    $mediaPosition = $entity->getBehaviorSetting('style_options', ['media_position', 'media_position'], 'default');
    $node = $entity->getParentEntity();
    if (!($node instanceof Node)) {
      return [];
    }
    if (!$node->hasField('schema_image')) {
      return [];
    }
    /** @var \Drupal\Core\Field\EntityReferenceFieldItemList $mediaReference */
    $mediaReference = $node->get('schema_image');
    if ($mediaReference->isEmpty()) {
      return [];
    }
    $media = $mediaReference->referencedEntities();
    // Should be only one.
    /** @var \Drupal\media\Entity\Media $image */
    $image = reset($media);
    $viewMode = match($mediaPosition) {
      'start', 'end' => '3_2',
      default => '3_1',
    };
    return $this->viewBuilder->view($image, $viewMode, $image->language()->getId());
  }

}
