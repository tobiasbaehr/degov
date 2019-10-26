<?php

namespace Drupal\node_action\Plugin\Action;

use Drupal\Core\Access\AccessResultForbidden;
use Drupal\Core\Action\Plugin\Action\EntityActionBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node_action\AccessChecker\ChangeModerationStateAction as AccessChecker;
use Drupal\node_action\AccessChecker\PublishedStateChange;
use Drupal\node_action\Redirector;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @Action(
 *   id = "node_action:entity:moderation_state_action:node",
 *   action_label = @Translation("Change moderation state"),
 *   type = "node"
 * )
 */
class ChangeModerationStateAction extends EntityActionBase {

  private $accessChecker;

  private $redirector;

  private $publishedStateChange;

  public function __construct(array $configuration, string $plugin_id, array $plugin_definition, EntityTypeManagerInterface $entity_type_manager, AccessChecker $accessChecker, Redirector $redirector, PublishedStateChange $publishedStateChange) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager);
    $this->accessChecker = $accessChecker;
    $this->redirector = $redirector;
    $this->publishedStateChange = $publishedStateChange;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('node_action.access_checker.change_moderation_state_action'),
      $container->get('node_action.redirector'),
      $container->get('node_action.access_checker.published_state_change')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function execute($entity = NULL): void {
  }

  /**
   * {@inheritdoc}
   */
  public function executeMultiple(array $entities): ?RedirectResponse {
    return $this->redirector->computeRedirectResponseByEntities($entities, 'node_action.moderation_state_form');
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    if (!$this->publishedStateChange->isAllowed($object)) {
      return $this->redirector->computeRedirectResponse('system.admin_content');
    }

    if (!$this->accessChecker->canAccess($object) instanceof AccessResultForbidden) {
      return TRUE;
    }
  }

}
