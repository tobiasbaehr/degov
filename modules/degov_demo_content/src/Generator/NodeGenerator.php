<?php

namespace Drupal\degov_demo_content\Generator;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\degov_demo_content\FileHandler\ParagraphsFileHandler;
use Drupal\node\Entity\Node;
use Drupal\pathauto\AliasCleanerInterface;
use Drupal\pathauto\PathautoState;

/**
 * Class NodeGenerator.
 *
 * @package Drupal\degov_demo_content\Generator
 */
class NodeGenerator extends ContentGenerator implements GeneratorInterface {

  /**
   * The alias cleaner.
   *
   * @var \Drupal\pathauto\AliasCleanerInterface
   */
  protected $aliasCleaner;

  /**
   * The paragraphs file handler.
   *
   * @var \Drupal\degov_demo_content\FileHandler\ParagraphsFileHandler
   */
  protected $paragraphsFileHandler;

  /**
   * NodeGenerator constructor.
   *
   * @param \Drupal\Core\Extension\ModuleHandler $moduleHandler
   *   Module handler.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager.
   * @param \Drupal\degov_demo_content\FileHandler\ParagraphsFileHandler $paragraphsFileHandler
   *   Paragraphs file handler.
   * @param \Drupal\pathauto\AliasCleanerInterface $aliasCleaner
   *   Alias cleaner.
   */
  public function __construct(ModuleHandler $moduleHandler, EntityTypeManagerInterface $entityTypeManager, ParagraphsFileHandler $paragraphsFileHandler, AliasCleanerInterface $aliasCleaner) {
    parent::__construct($moduleHandler, $entityTypeManager, $paragraphsFileHandler);
    $this->aliasCleaner = $aliasCleaner;
    $this->entityType = 'node';
    $this->paragraphsFileHandler = $paragraphsFileHandler;
  }

  /**
   * Generates content.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function generateContent(): void {
    $teaserPage = NULL;
    $nodeIds = [];

    foreach ($this->loadDefinitions('node.yml') as $srcId => $rawNode) {
      $paragraphs = [];

      if (isset($rawNode['field_content_paragraphs'])) {
        $paragraphs['field_content_paragraphs'] = $rawNode['field_content_paragraphs'];
      }
      if (isset($rawNode['field_header_paragraphs'])) {
        $paragraphs['field_header_paragraphs'] = $rawNode['field_header_paragraphs'];
      }
      if (isset($rawNode['field_sidebar_right_paragraphs'])) {
        $paragraphs['field_sidebar_right_paragraphs'] = $rawNode['field_sidebar_right_paragraphs'];
      }

      $rawNode['field_tags'] = [
        ['target_id' => $this->getDemoContentTagId()],
      ];

      $paragraphs = array_filter($paragraphs);
      unset($rawNode['field_content_paragraphs'], $rawNode['field_header_paragraphs'], $rawNode['field_sidebar_right_paragraphs']);

      $this->generateParagraphs($paragraphs, $rawNode);
      $this->prepareValues($rawNode);
      $rawNode['path'] = [
        'alias'    => '/degov-demo-content/' . $this->aliasCleaner->cleanString($rawNode['title']),
        'pathauto' => PathautoState::SKIP,
      ];
      // If no "created" date is defined in definitions, we  generate a unique
      // number with 5 digits based on $srcId (% digits are about a day in Unix time
      // 86400s->1 day) and add it to DEGOV_DEMO_CONTENT_CREATED_TIMESTAMP.
      // A manual date defined date should be  > DEGOV_DEMO_CONTENT_CREATED_TIMESTAMP + 100.000 to be stable.
      $rawNode['created'] = isset($rawNode['created']) ? $rawNode['created'] : $this->getCreatedTimestamp($srcId);

      $node = Node::create($rawNode);
      $node->save();

      // Use first node for teasers.
      if ($teaserPage === NULL) {
        $teaserPage = $node;
        $this->setFrontPage('/node/' . $teaserPage->id());
      }
      else {
        if ($rawNode['type'] !== 'faq') {
          $nodeIds[] = $node->id();
        }
      }
      /*
       * Make sure Nodes are not all created the same second,
       * otherwise views will display them in random order.
       */
      sleep(1);
    }
    $this->generateNodeReferenceParagraphs($teaserPage, $nodeIds);
    $this->generateMediaReferenceParagraphs($teaserPage);
  }

  /**
   * Set front page.
   *
   * @param string $path_to_set
   *   Path to set.
   */
  private function setFrontPage($path_to_set) {
    $original_front_page = \Drupal::config('degov.degov_demo_content')->get('original_front_page');
    if (empty($original_front_page)) {
      // Save original front page.
      $front = \Drupal::config('system.site')->get('page.front');
      \Drupal::configFactory()->getEditable('degov.degov_demo_content')->set('original_front_page', $front)->save();
    }
    \Drupal::configFactory()->getEditable('system.site')->set('page.front', $path_to_set)->save();
  }

  /**
   * Reset front page.
   */
  private function resetFrontPage() {
    $original_front_page = \Drupal::config('degov.degov_demo_content')->get('original_front_page');
    if (!empty($original_front_page)) {
      $this->setFrontPage($original_front_page);
      \Drupal::configFactory()->getEditable('degov.degov_demo_content')->set('original_front_page', NULL)->save();
    }
  }

  /**
   * Deletes the generated content.
   */
  public function deleteContent(): void {
    parent::deleteContent();
    $this->resetFrontPage();
  }

  /**
   * Reset content.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function resetContent(): void {
    $this->deleteContent();
    $this->generateContent();
  }

}
