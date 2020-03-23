<?php

namespace Drupal\entity_reference_timer\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\entity_reference_display\Plugin\Field\FieldFormatter\EntityReferenceDisplayFormatter;
use Drupal\entity_reference_timer\Service\EntityReferenceTimerVisibilityService;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'entity_reference_display_default' formatter.
 *
 * @FieldFormatter(
 *   id = "entity_reference_date_display_default",
 *   label = @Translation("Selected display mode for entity reference date"),
 *   description = @Translation("This formatter allows you to render referenced entities by selected display mode."),
 *   field_types = {
 *     "entity_reference_date",
 *   }
 * )
 */
class EntityReferenceDateDisplayFormatter extends EntityReferenceDisplayFormatter {

  /**
   * The entity reference timer visibility service.
   *
   * @var \Drupal\entity_reference_timer\Service\EntityReferenceTimerVisibilityService
   */
  protected $visibilityService;

  /**
   * {@inheritdoc}
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, LoggerChannelFactoryInterface $logger_factory, EntityTypeManagerInterface $entity_type_manager, EntityDisplayRepositoryInterface $entity_display_repository, EntityReferenceTimerVisibilityService $visibility_service) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings, $logger_factory, $entity_type_manager, $entity_display_repository);
    $this->visibilityService = $visibility_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): EntityReferenceDateDisplayFormatter {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('logger.factory'),
      $container->get('entity_type.manager'),
      $container->get('entity_display.repository'),
      $container->get('entity_reference_timer.visibility_service')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode): array {
    foreach ($items as $item) {
      $this->visibilityService->addExpirationDateToParentNode($item);
    }
    $items->filter([$this->visibilityService, 'isVisible']);
    return parent::viewElements($items, $langcode);
  }

}
