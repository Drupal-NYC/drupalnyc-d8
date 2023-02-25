<?php

namespace Drupal\dnyc_migrate\Plugin\migrate\process;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\file\Entity\File;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\MigrateProcessInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @MigrateProcessPlugin(
 *   id = "bg_image_file"
 * )
 */
class BackgroundImageFiles extends ProcessPluginBase implements MigrateProcessInterface, ContainerFactoryPluginInterface {

  /**
   * Injected service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Injected service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->fileSystem = $container->get('file_system');
    return $instance;
  }

  /**
   * Moves the associated file and returns the target id provided.
   *
   * @param $value
   *   Expected to be File entity id.
   *   The migration in which this process is being executed.
   * @param \Drupal\migrate\Row $row
   *   The row from the source to process.
   * @param string $destination_property
   *   The destination property currently worked on. This is only used together
   *   with the $row above.
   *
   * @return \Drupal\file\Entity\File
   *   The given value if it is in fact a file entity id.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $file = $this->entityTypeManager->getStorage('file')->load($value['target_id']);
    if ($file instanceof File) {
      $created = DrupalDateTime::createFromTimestamp($file->getCreatedTime());
      $base = 'public://' . $created->format('Y') . '-' . $created->format('m');
      $this->fileSystem->prepareDirectory($base, FileSystemInterface::CREATE_DIRECTORY);
      $destination = "$base/" . $file->getFilename();
      $result = $this->fileSystem->move($file->getFileUri(), $destination);
      $file->setFileUri($result);
      $file->save();
      return $file;
    }
    else {
      return NULL;
    }
  }
}
