<?php

namespace Drupal\degov_demo_content\Factory;

use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\pathauto\AliasCleanerInterface;
use Drupal\pathauto\PathautoState;

class NodeFactory extends ContentFactory {

  /**
   * Generates a set of node entities.
   */
  protected $entityType = 'node';

  protected $mediaGenerator;

  private $imageCounter = 0;

  /**
   * The alias cleaner.
   *
   * @var \Drupal\pathauto\AliasCleanerInterface
   */
  protected $aliasCleaner;

  public function __construct(MediaFactory $mediaGenerator, AliasCleanerInterface $aliasCleaner) {
    $this->mediaGenerator = $mediaGenerator;
    $this->aliasCleaner = $aliasCleaner;
    parent::__construct();
  }

  public function generateContent(): void {
    $teaserPage = NULL;
    $nodeIds = [];

    foreach ($this->loadDefinitions('node.yml') as $rawNode) {

      $paragraphs['field_content_paragraphs'] = $rawNode['field_content_paragraphs'];
      $paragraphs['field_header_paragraphs'] = $rawNode['field_header_paragraphs'];
      $paragraphs['field_sidebar_paragraphs'] = $rawNode['field_sidebar_paragraphs'];
      $paragraphs = array_filter($paragraphs);
      unset($rawNode['field_content_paragraphs'], $rawNode['field_header_paragraphs'], $rawNode['field_sidebar_paragraphs']);

      $this->generateParagraphsForNode($paragraphs, $rawNode);
      $this->prepareValues($rawNode);
      $rawNode['path'] = ['alias' => '/degov-demo-content/' . $this->aliasCleaner->cleanString($rawNode['title']), 'pathauto' => PathautoState::SKIP];
      $node = Node::create($rawNode);
      $node->save();

      /**
       * Use first node for teasers
       */
      if ($teaserPage === NULL) {
        $teaserPage = $node;
      }
      else {
        $nodeIds[] = $node->id();
      }
    }
    $this->generateNodeReferenceParagraphs($teaserPage, $nodeIds);
  }


  protected function generateParagraphsForNode(array $rawParagraphReferences, &$rawNode) {
    foreach ($rawParagraphReferences as $type => $rawParagraphReferenceElements) {
      foreach ($rawParagraphReferenceElements as $rawParagraphReference) {
        $rawParagraph = $this->loadDefinitionByNameTag('paragraphs', $rawParagraphReference);
        $this->prepareValues($rawParagraph);
        $paragraph = Paragraph::create($rawParagraph);
        $paragraph->save();
        $rawNode[$type][] = $paragraph;
      }
    }
  }

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

  public function resetContent(): void {
    $this->deleteContent();
    $this->generateContent();
  }

  protected function getImages(): array {
    $mediaIds = \Drupal::entityQuery('media')
      ->condition('bundle', 'image')
      ->condition('field_tags', $this->getDemoContentTagId())->execute();
    return $mediaIds;
  }


  protected function getImage(): Media {
    $images = $this->getImages();
    $this->imageCounter++;
    $index = $this->imageCounter % \count($images);
    $keys = array_keys($images);
    return Media::load($images[$keys[$index]]);
  }
}
