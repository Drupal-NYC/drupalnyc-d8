<?php declare(strict_types = 1);

namespace Drupal\dnyc_amazee\Plugin\Block;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides an amazee link block.
 *
 * @Block(
 *   id = "dnyc_amazee_link",
 *   admin_label = @Translation("Amazee Link"),
 *   category = @Translation("Sponsor"),
 * )
 */
final class AmazeeLinkBlock extends BlockBase {


  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state): array {
    $form['amazee_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Amazee URL'),
      '#description' => $this->t('Amazee URL to link the image'),
      '#default_value' => $this->configuration['amazee_url'] ?? 'https://www.amazee.io',
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    // Instantiate UrlHelper object to validate the URL's.
    $url_helper = new UrlHelper();

    // Build an array of the links that need validating. If more fields are
    // added later, add another entry to the links array.
    $links = [];
    $links['amazee_url'] = $form_state->getValue('amazee_url');
    // Validate and set errors where appropriate.
    foreach ($links as $key => $link) {
      if ($link == '') {
        break;
      }
      $validity = $url_helper->isValid($link, TRUE);
      if ($validity != TRUE) {
        $form_state->setErrorByName($key, "The value must be a full URL similar to http://www.example.com.");
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state): void {
    $this->configuration['amazee_url'] = $form_state->getValue('amazee_url');
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $build = [];
    $build['dcnyc_amazee_footer'] = [
      '#theme' => 'dcnyc_amazee_footer',
      '#amazee_url' => $this->configuration['amazee_url'],
      '#amazee_image' => '/modules/custom/dnyc_amazee/amazee-io-mirantis.svg',
    ];
    return $build;
  }

}
