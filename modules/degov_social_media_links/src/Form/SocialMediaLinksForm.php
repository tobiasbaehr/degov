<?php

declare(strict_types=1);

namespace Drupal\degov_social_media_links\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Component\Transliteration\TransliterationInterface;
use Drupal\Core\Language\LanguageInterface;

/**
 * Configure social media links settings.
 */
class SocialMediaLinksForm extends EntityForm {

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Transliteration is used to derivate id from name.
   *
   * @var \Drupal\Component\Transliteration\TransliterationInterface
   */
  protected $transliteration;

  /**
   * Constructs a SocialMediaLinksForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entityTypeManager.
   * @param \Drupal\Component\Transliteration\TransliterationInterface $transliteration
   *   The transliteration service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, TransliterationInterface $transliteration) {
    $this->entityTypeManager = $entityTypeManager;
    $this->transliteration = $transliteration;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new self(
      $container->get('entity_type.manager'),
      $container->get('transliteration')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state): array {
    $form = parent::form($form, $form_state);
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Social media name'),
      '#size' => 30,
      '#pattern' => '^[A-Za-z]+$',
      '#default_value' => $this->entity->get('label'),
      '#required' => TRUE,
    ];

    $form['url'] = [
      '#type' => 'url',
      '#title' => $this->t('Url'),
      '#size' => 30,
      '#default_value' => $this->entity->get('url'),
      '#description' => $this->t('Social media url. E.g https://twitter.com/publicplan_gmbh'),
      '#required' => TRUE,
    ];

    $form['icon'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Icon'),
      '#size' => 30,
      '#pattern' => '^[a-z][a-z0-9- ]+[a-z0-9]$',
      '#default_value' => $this->entity->get('icon'),
      '#description' => $this->t('Click to select an icon.'),
      '#required' => TRUE,
      '#attributes' => [
        'autocomplete' => 'off',
        // Enables degov_fa_icon_picker.
        'class' => [
          'fa-icon-select',
        ],
      ],
    ];

    if (!$this->entity->isNew()) {
      $form['id'] = [
        '#type' => 'machine_name',
        '#default_value' => $this->entity->id(),
        '#machine_name' => [
          'exists' => [$this, 'exist'],
        ],
        '#disabled' => !$this->entity->isNew(),
      ];
    }

    $form['#attached']['library'][] = 'degov_fa_icon_picker/degov_fa_icon_picker';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $configEntity = $this->entity;
    $name = $form_state->getValue('label');
    if (!$form_state->getValue('id')) {
      $new_value = $this->transliteration->transliterate($name, LanguageInterface::LANGCODE_DEFAULT, '_');
      $new_value = strtolower($new_value);
      $new_value = preg_replace('/[^a-z0-9_]+/', '_', $new_value);
      $configEntity->set('id', preg_replace('/_+/', '_', $new_value));
    }
    $status = $configEntity->save();
    if ($status === SAVED_NEW) {
      $this->messenger()->addMessage($this->t('%label link created.', [
        '%label' => $configEntity->label(),
      ]));
    }
    else {
      $this->messenger()->addMessage($this->t('%label link updated.', [
        '%label' => $configEntity->label(),
      ]));
    }
    $form_state->setRedirect('entity.degov_social_media_links.collection');
  }

  /**
   * Helper function to check whether a SocialMediaLinks configuration entity exists.
   */
  public function exist($id): bool {
    $entity = $this->entityTypeManager->getStorage('degov_social_media_links')->getQuery()
      ->condition('id', $id)
      ->execute();
    return (bool) $entity;
  }

}
