<?php

namespace Drupal\instagram_feed_block\Form;

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
    return 'instagram_feed_block_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'instagram_feed_block.api_key',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('instagram_feed_block.settings');
    $form['user'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Your user name'),
      '#default_value' => $config->get('user'),
    ];
    $form['number_of_posts'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Number of listed posts'),
      '#default_value' => $config->get('number_of_posts'),
    ];
    $form['number_of_characters'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Number of characters post\'s caption'),
      '#default_value' => $config->get('number_of_characters'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = \Drupal::service('config.factory')->getEditable('instagram_feed_block.settings');
    $config->set('user', $values['user'])->save();
    $config->set('number_of_posts', $values['number_of_posts'])->save();
    $config->set('number_of_characters', $values['number_of_characters'])->save();
  }

}
