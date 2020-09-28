<?php

namespace Drupal\degov_hyphenopoly\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure example settings for this site.
 */
class HyphenopolySettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'degov_hyphenopoly.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'degov_hyphenopoly.settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $cssSelectors = $this->config(static::SETTINGS)->get('hyphenopoly_selectors');

    $form['into'] = [
      '#markup' => '<p>JavaScript-polyfill for hyphenation in HTML based on <a href="https://github.com/mnater/Hyphenopol">Hyphenopoly</a>. '
      . 'You can configure for which CSS selectors hyphenation should be applied.</p>',
    ];

    $form['hyphenopoly_selectors'] = [
      '#type' => 'textarea',
      '#title' => $this->t('CSS selectors'),
      '#required' => TRUE,
      '#description' => t('Specify one selector per line. For example <code>.normal-page__teaser-title</code> or <code>#my-field</code>'),
      '#default_value' => is_array($cssSelectors) ? $this->settingsToString($cssSelectors) : NULL,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    foreach ($this->settingsToArray($form_state->getValue('hyphenopoly_selectors')) as $ln => $line) {
      $class_prefix = substr($line, 0, 1);
      if (!($class_prefix === '.' || $class_prefix === '#')) {
        $form_state->setErrorByName('hyphenopoly_selectors', $this->t('CSS selector name must start with a dot or a hash at line @line.', ['@line' => $ln + 1]));
      }
      // @see https://regex101.com/r/mA6cA0/13
      if (!preg_match('/^(\.?\#?[_a-zA-Z]+[_a-zA-Z0-9-]*)$/', $line)) {
        $form_state->setErrorByName('hyphenopoly_selectors', $this->t('Line number @line is not valid CSS selector', ['@line' => $ln + 1]));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $this->settingsToArray($form_state->getValue('hyphenopoly_selectors'));
    $this->configFactory->getEditable(static::SETTINGS)
      ->set('hyphenopoly_selectors', $values)
      ->save();
    parent::submitForm($form, $form_state);
  }

  /**
   * Converts a multiline text field to array.
   *
   * @param string $str
   *   Multiline input string.
   */
  protected function settingsToArray(string $str) :array {
    return explode('\n', str_replace(["\r\n", "\n\r", "\r"], '\n', trim($str)));
  }

  /**
   * Convert Array data to multiline string.
   *
   * @return string
   *   Multiline input string.
   */
  protected function settingsToString(array $data) :string {
    return implode("\n", $data);
  }

}
