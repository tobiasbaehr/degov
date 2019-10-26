<?php

namespace Drupal\node_action\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;


class Author extends FormBase {

  use ActionFormTrait;

  private $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  public function getFormId(): string {
    return 'moderation_state_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state): array {
    $this->removeMessageFromDefaultAction();

    $entityIds = $this->getRequest()->get('entityIds');

    $nodesList = $this->putTogetherHTMLList($entityIds);

    $form['entity_info'] = [
      '#markup' => $this->t('Nodes which will be affected by this action:') . $nodesList
    ];

    $form['author'] = [
      '#type'          => 'select',
      '#title'         => $this->t('Users'),
      '#size'          => 5,
      '#required'      => TRUE,
      '#options'       => $this->getAllUsersAsOptions(),
    ];

    $form['entity_ids'] = [
      '#type'  => 'hidden',
      '#value' => json_encode($entityIds),
    ];

    $form['submit'] = [
      '#type'  => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $entityIds = json_decode($form_state->getValue('entity_ids'), TRUE);

    foreach ($entityIds as $entityId => $nodeTitle) {
      $this->setAuthor($form_state, $entityId);
      $this->messenger()->addMessage($this->t('Action was performed successfully.') . ' ' . $this->t('Applied author change with user display name @userDisplayName on @num nodes.',
        [
          '@userDisplayName' => $this->entityTypeManager->getStorage('user')->load($form_state->getValue('author'))->getDisplayName(),
          '@num'             => \count($entityIds),
        ])
      );
    }

    $this->redirectToContentOverview();
  }

  private function getAllUsersAsOptions(): array {
    /**
     * @var User[] $users
     */
    $users = $this->entityTypeManager->getStorage('user')->loadMultiple();

    $options = [];

    foreach ($users as $user) {
      $options[$user->id()] = $user->getDisplayName();
    }

    return $options;
  }

  private function setAuthor(FormStateInterface $form_state, int $entityId): void {
    $node = Node::load($entityId);
    $node->set('uid', $form_state->getValue('author'));
    $node->save();
  }

}
