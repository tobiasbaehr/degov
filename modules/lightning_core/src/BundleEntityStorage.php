<?php

namespace Drupal\lightning_core;

use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\Core\Entity\EntityAccessControlHandlerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A storage handler for entity types that are bundles of other entity types.
 */
final class BundleEntityStorage extends ConfigEntityStorage {

  /**
   * The access control handler.
   *
   * @var \Drupal\Core\Entity\EntityAccessControlHandlerInterface
   */
  protected $accessHandler;

  /**
   * @param \Drupal\Core\Entity\EntityAccessControlHandlerInterface $accessHandler
   */
  public function setAccessHandler(EntityAccessControlHandlerInterface $accessHandler): void {
    $this->accessHandler = $accessHandler;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    $instance = parent::createInstance($container, $entity_type);
    $instance->setAccessHandler($container->get('entity_type.manager')->getAccessControlHandler($entity_type->getBundleOf()));
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function loadMultiple(array $ids = NULL, $check_access = FALSE) {
    if ($check_access) {
      $ids = array_filter(
        $ids ?: $this->getQuery()->execute(),
        [$this->accessHandler, 'createAccess']
      );
    }
    return parent::loadMultiple($ids);
  }

}
