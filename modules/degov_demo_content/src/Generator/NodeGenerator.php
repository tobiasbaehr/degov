<?php

namespace Drupal\degov_demo_content\Generator;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\pathauto\AliasCleanerInterface;
use Drupal\pathauto\PathautoState;

/**
 * Class NodeGenerator.
 *
 * @package Drupal\degov_demo_content\Generator
 */
class NodeGenerator extends ContentGenerator implements GeneratorInterface {

  /**
   * Generates a set of node entities.
   *
   * @var \Drupal\degov_demo_content\Generator\MediaGenerator
   */
  protected $mediaGenerator;

  /**
   * The alias cleaner.
   *
   * @var \Drupal\pathauto\AliasCleanerInterface
   */
  protected $aliasCleaner;

  /**
   * NodeGenerator constructor.
   *
   * @param \Drupal\Core\Extension\ModuleHandler $moduleHandler
   *   Module handler.
   * @param Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager.
   * @param \Drupal\degov_demo_content\Generator\MediaGenerator $mediaGenerator
   *   Media generator.
   * @param \Drupal\pathauto\AliasCleanerInterface $aliasCleaner
   *   Alias cleaner.
   */
  public function __construct(ModuleHandler $moduleHandler, EntityTypeManagerInterface $entityTypeManager, MediaGenerator $mediaGenerator, AliasCleanerInterface $aliasCleaner) {
    parent::__construct($moduleHandler, $entityTypeManager);
    $this->mediaGenerator = $mediaGenerator;
    $this->aliasCleaner = $aliasCleaner;
    $this->entityType = 'node';
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

      $this->generateParagraphsForNode($paragraphs, $rawNode);
      $this->prepareValues($rawNode);
      $rawNode['path'] = [
        'alias'    => '/degov-demo-content/' . $this->aliasCleaner->cleanString($rawNode['title']),
        'pathauto' => PathautoState::SKIP,
      ];
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
   * Generate paragraphs for node.
   *
   * @param array $rawParagraphReferences
   *   Raw paragraph references.
   * @param mixed $rawNode
   *   Raw node.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function generateParagraphsForNode(array $rawParagraphReferences, &$rawNode): void {
    foreach ($rawParagraphReferences as $type => $rawParagraphReferenceElements) {
      foreach ($rawParagraphReferenceElements as $rawParagraphReference) {
        $rawParagraph = $this->loadDefinitionByNameTag('paragraphs', $rawParagraphReference);
        $this->prepareValues($rawParagraph);
        $this->resolveEncapsulatedParagraphs($rawParagraph);
        $paragraph = Paragraph::create($rawParagraph);
        $paragraph->save();
        $rawNode[$type][] = $paragraph;
      }
    }
  }

  /**
   * Resolve encapsulated paragraphs.
   *
   * @param mixed $rawParagraph
   *   Raw paragraph.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function resolveEncapsulatedParagraphs(&$rawParagraph): void {
    foreach ($rawParagraph as $index => $rawField) {
      if (\is_array($rawField)) {
        foreach ($rawField as $innerIndex => $rawValue) {
          $fieldName = str_replace('paragraph_reference_', '', $rawValue);
          if (strpos($rawValue, 'paragraph_reference_') !== FALSE) {
            $rawInnerParagraph = $this->loadDefinitionByNameTag('paragraphs', $fieldName);
            $this->prepareValues($rawInnerParagraph);
            $innerParagraph = Paragraph::create($rawInnerParagraph);
            $innerParagraph->save();
            $rawParagraph[$index][$innerIndex] = $innerParagraph;
          }
        }
      }
    }
  }

  /**
   * Generate node reference paragraphs.
   *
   * @param \Drupal\node\Entity\Node $teaserPage
   *   Teaser page node.
   * @param array $nodeIds
   *   Node IDs.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function generateNodeReferenceParagraphs(Node $teaserPage, array $nodeIds): void {
    $paragraphs = [];
    foreach ($this->loadDefinitionByNameType('paragraphs', 'node_reference') as $rawParagraph) {
      $rawParagraph['field_sub_title'] = $this->generateBlindText(3);
      $rawParagraph['field_node_reference_nodes'] = $nodeIds;
      $paragraph = Paragraph::create($rawParagraph);
      $paragraph->save();
      $paragraphs[] = $paragraph;
    }
    $teaserPage->set('field_content_paragraphs', $paragraphs);
    $teaserPage->save();
  }

  /**
   * Generates Media reference paragraph.
   *
   * @param \Drupal\node\Entity\Node $teaserPage
   *   The Node entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function generateMediaReferenceParagraphs(Node $teaserPage): void {
    $rawParagraph = $this->loadDefinitionByNameTag('paragraphs', 'media_reference_citation_front');
    $this->prepareValues($rawParagraph);
    $this->resolveEncapsulatedParagraphs($rawParagraph);
    unset($rawParagraph['field_title']);

    $paragraph = Paragraph::create($rawParagraph);
    $paragraph->save();

    $teaserPage->get('field_content_paragraphs')->appendItem($paragraph);
    $teaserPage->save();
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
