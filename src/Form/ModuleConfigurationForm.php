<?php

namespace Drupal\youtube_feed_block\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines a form that configures forms module settings.
 */
class ModuleConfigurationForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'youtube_feed_block_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'youtube_feed_block.api_key',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('youtube_feed_block.settings');
    $form['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your API key'),
      '#default_value' => $config->get('api_key'),
    ];
    $form['channel'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your channel name or channel Id'),
      '#default_value' => $config->get('channel'),
    ];
    $form['number_of_videos'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Number of listed videos'),
      '#default_value' => $config->get('number_of_videos'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = \Drupal::service('config.factory')->getEditable('youtube_feed_block.settings');
    $config->set('api_key', $values['api_key'])->save();
    $config->set('channel', $values['channel'])->save();
    $config->set('number_of_videos', $values['number_of_videos'])->save();
  }

}
