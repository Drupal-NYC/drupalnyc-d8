<?php

namespace Drupal\entity_embed\Plugin\CKEditor4To5Upgrade;

use Drupal\ckeditor5\HTMLRestrictions;
use Drupal\ckeditor5\Plugin\CKEditor4To5UpgradePluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\filter\FilterFormatInterface;

/**
 * Provides the CKEditor 4 to 5 upgrade for the placeholder CKEditor plugin.
 *
 * @CKEditor4To5Upgrade(
 *   id = "entity_embed",
 *   cke4_buttons = {
 *     "drupalentity",
 *   },
 *   cke4_plugin_settings = {
 *   }
 * )
 */
class EntityEmbed extends PluginBase implements CKEditor4To5UpgradePluginInterface {

  /**
   * {@inheritdoc}
   */
  public function mapCKEditor4ToolbarButtonToCKEditor5ToolbarItem(
    string $cke4_button,
    HTMLRestrictions $text_format_html_restrictions
  ): ?array {
    if ($cke4_button === 'drupalentity') {
      return ['entity_embed_drupalentity'];
    }
    else {
      throw new \OutOfBoundsException();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function mapCKEditor4SettingsToCKEditor5Configuration(string $cke4_plugin_id, array $cke4_plugin_settings): ?array {
    throw new \OutOfBoundsException();
  }

  /**
   * {@inheritdoc}
   */
  public function computeCKEditor5PluginSubsetConfiguration(string $cke5_plugin_id, FilterFormatInterface $text_format): ?array {
    throw new \OutOfBoundsException();
  }

}
