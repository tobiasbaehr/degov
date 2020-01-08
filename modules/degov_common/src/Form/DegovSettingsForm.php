<?php

namespace Drupal\degov_common\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DegovSettingsForm.
 *
 * @package Drupal\degov_views_helper
 */
class DegovSettingsForm extends ConfigFormBase {

  /**
   * Gets the configuration names that will be editable.
   *
   * @return array
   *   An array of configuration object names that are editable if called in
   *   conjunction with the trait's config() method.
   */
  protected function getEditableConfigNames() {
    return [
      'degov_common.default_settings',
    ];
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'degov_default_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('degov_common.default_settings');

    $form['help'] = [
      '#type' => 'markup',
      '#markup' => '<p>' . $this->t('This is configuration section of deGov distribution') . '</p>',
    ];
    $form['privacy_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Privacy URL Path'),
      '#description' => $this->t('The path at which the privacy terms can be found.'),
      '#default_value' => $config->get('privacy_url') ?: '/datenschutzhinweise',
      '#required' => TRUE,
    ];
    $form['netiquette_url'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Netiquette URL Path'),
      '#description' => $this->t('The path at which the netiquette can be found.'),
      '#default_value' => $config->get('netiquette_url') ?: '/netiquette',
      '#required' => TRUE,
    ];
    $form['youtube_apikey'] = [
      '#type' => 'textfield',
      '#title' => t('Youtube API Key'),
      '#description' => $this->t('Obtain a Youtube API key at <a href="@link">@link</a>', [
        '@link' => 'https://console.developers.google.com/apis/credentials',
      ]),
      '#default_value' => $config->get('youtube_apikey') ?: '',
      '#required' => FALSE,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::service('config.factory')->getEditable('degov_common.default_settings');
    $config
      ->set('privacy_url', $form_state->getValue('privacy_url'))
      ->set('netiquette_url', $form_state->getValue('netiquette_url'))
      ->set('youtube_apikey', $form_state->getValue('youtube_apikey'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
