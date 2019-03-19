<?php

namespace Drupal\media_file_links\Plugin\Linkit\Matcher;

use Drupal\Core\Form\FormStateInterface;
use Drupal\linkit\Plugin\Linkit\Matcher\EntityMatcher;


/**
 * Provides specific LinkIt matchers for our custom entity type.
 *
 * @Matcher(
 *   id = "entity:media_file_links",
 *   label = @Translation("Your custom content entity"),
 *   target_entity = "media",
 *   provider = "media_file_links"
 * )
 */

class MediaFileMatcher extends EntityMatcher {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration() + [
        'result_description' => '',
        'bundles' => [],
        'group_by_bundle' => FALSE,
      ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $entity_type = $this->entityManager->getDefinition($this->target_type);
    $form['result_description'] = [
      '#title' => $this->t('Result description'),
      '#type' => 'textfield',
      '#default_value' => $this->configuration['result_description'],
      '#size' => 120,
      '#maxlength' => 255,
      '#weight' => -100,
    ];

    // Filter the possible bundles to use if the entity has bundles.
    if ($entity_type->hasKey('bundle')) {
      // Group the results by bundle.
      $form['group_by_bundle'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Group by bundle'),
        '#default_value' => $this->configuration['group_by_bundle'],
        '#weight' => -50,
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function execute($string) {
    error_log('suggest!');
  }
}
