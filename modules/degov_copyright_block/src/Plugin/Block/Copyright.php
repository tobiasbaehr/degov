<?php

namespace Drupal\degov_copyright_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a copyright block with the current year and the site name.
 *
 * @Block(
 *   id = "degov_copyright_block",
 *   admin_label = @Translation("Copyright"),
 * )
 */
class Copyright extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $blockConfiguration = $this->getConfiguration();
    $form['copyright_text'] = [
      '#type'          => 'textfield',
      '#title'         => new TranslatableMarkup('Copyright text'),
      '#description'   => new TranslatableMarkup('This text will be displayed in the footer after the copyright symbol and the current year.'),
      '#default_value' => $blockConfiguration['copyright_text'] ?? '',
      '#attributes'    => [
        'placeholder' => \Drupal::config('system.site')->get('name'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('copyright_text', $form_state->getValue('copyright_text'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $blockConfiguration = $this->getConfiguration();

    $block = [
      '#theme'            => 'degov_copyright_block',
      '#date'             => date('Y'),
      '#copyright_holder' => !empty(trim($blockConfiguration['copyright_text'])) ? $blockConfiguration['copyright_text'] : \Drupal::config('system.site')->get('name'),
    ];

    return $block;
  }

}
