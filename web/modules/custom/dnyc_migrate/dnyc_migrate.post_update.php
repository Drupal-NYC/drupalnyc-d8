<?php

/**
 * @file move background images to media.
 */

use Drupal\file\Entity\File;
use Drupal\Core\File\FileSystemInterface;

/**
 * Move image files from background image entities to media.
 */
function dnyc_migrate_post_update_background_2() {
  $bg_storage = \Drupal::entityTypeManager()->getStorage('background_image');
  $media_storage = \Drupal::entityTypeManager()->getStorage('media');
  /** @var \Drupal\Core\File\FileSystem $file_system */
  $file_system = \Drupal::service('file_system');
  $entities = $bg_storage->loadMultiple();
  $base = 'public://2022-11/';
  $file_system->prepareDirectory($base, FileSystemInterface::CREATE_DIRECTORY);
  foreach ($entities as $entity) {
    /** @var \Drupal\media\Entity\Media $image */
    $image = $media_storage->create([
      'bundle' => 'image',
      'name' => strip_tags($entity->label()),
    ]);
    if ($image->hasField('field_media_image')) {
      $image->field_media_image->setValue($entity->image->getValue());
    }
    // Move the image file.
    /** @var \Drupal\file\Plugin\Field\FieldType\FileItem $file_item */
    $file_item = $image->field_media_image->first();
    $file = $file_item->entity;
    if ($file instanceof File) {
      $destination = $base . $file->getFilename();
      $result = $file_system->move($file->getFileUri(), $destination);
      $file->setFileUri($result);
      $file->save();
    }
    $image->save();
    $entity->delete();
  }
}
