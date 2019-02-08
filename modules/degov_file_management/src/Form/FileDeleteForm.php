<?php

namespace Drupal\degov_file_management\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a file.
 *
 * @internal
 */
class FileDeleteForm extends ConfirmFormBase {

  /**
   * The file.
   *
   * @var \Drupal\file\FileInterface
   */
  protected $file;

  /**
   * The file storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $fileStorage;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Constructs a new FileDeleteForm.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $file_storage
   *   The file storage.
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection.
   */
  public function __construct(EntityStorageInterface $file_storage, Connection $connection) {
    $this->fileStorage = $file_storage;
    $this->connection = $connection;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $entity_manager = $container->get('entity.manager');
    return new static(
      $entity_manager->getStorage('file'),
      $container->get('database')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'degov_file_management_file_delete_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return t('Are you sure you want to delete the file %filename? Existing references to this file may break.', ['%filename' => $this->file->getFilename()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromUri('/admin/content/files');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $fid = NULL) {
    $this->file = $this->fileStorage->load($fid);
    $form = parent::buildForm($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
//    $this->nodeStorage->deleteRevision($this->revision->getRevisionId());
//
//    $this->logger('content')->notice('@type: deleted %title revision %revision.', ['@type' => $this->revision->bundle(), '%title' => $this->revision->label(), '%revision' => $this->revision->getRevisionId()]);
//    $node_type = $this->nodeTypeStorage->load($this->revision->bundle())->label();
//    $this->messenger()
//      ->addStatus($this->t('Revision from %revision-date of @type %title has been deleted.', [
//        '%revision-date' => format_date($this->revision->getRevisionCreationTime()),
//        '@type' => $node_type,
//        '%title' => $this->revision->label(),
//      ]));
//    $form_state->setRedirect(
//      'entity.node.canonical',
//      ['node' => $this->revision->id()]
//    );
//    if ($this->connection->query('SELECT COUNT(DISTINCT vid) FROM {node_field_revision} WHERE nid = :nid', [':nid' => $this->revision->id()])->fetchField() > 1) {
//      $form_state->setRedirect(
//        'entity.node.version_history',
//        ['node' => $this->revision->id()]
//      );
//    }
  }

}
