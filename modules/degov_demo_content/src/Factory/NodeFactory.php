<?php

namespace Drupal\degov_demo_content\Factory;

use Drupal\media\Entity\Media;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;

class NodeFactory extends ContentFactory {


  private $imageCounter = 0;

  /**
   * Generates a set of node entities.
   */
  protected $entityType = 'node';

  protected $mediaGenerator;

  public function __construct(MediaFactory $mediaGenerator) {
    $this->mediaGenerator = $mediaGenerator;
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

  protected function prepareValues(array &$rawParagraph) {
    foreach ($rawParagraph as $index => $value) {
      switch ($value) {
        case '{{SUBTITLE}}':
          $rawParagraph[$index] = $this->generateBlindText(5);
          break;
        case '{{TEXT}}':
          $rawParagraph[$index] = $this->generateBlindText(50);
          break;
        case '{{MEDIA_IMAGE_ID}}':
          $rawParagraph[$index] = ['target_id' => $this->getImage()->id()];
          break;
        case '{{DEMOTAG}}':
          $rawParagraph[$index] = ['target_id' => $this->getDemoContentTagId()];
          break;
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
