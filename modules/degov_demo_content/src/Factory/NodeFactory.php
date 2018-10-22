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

  private $Ids = NULL;

  protected $entityType = 'node';

  protected $mediaGenerator;

  public function __construct(MediaFactory $mediaGenerator) {
    $this->mediaGenerator = $mediaGenerator;
    parent::__construct();
  }

  public function generateContent(): void {
    $rawNodes = $this->loadDefinitions('node.yml');

    $teaserPage = NULL;

    $teaserLongText = Paragraph::create([
      'type' => 'node_reference',
      'field_title' => $this->generateBlindText(4),
      'field_sub_title' => $this->generateBlindText(3),
      'field_node_reference_viewmode' => 'long_text',
    ]);
    $teaserLongText->save();

    foreach ($rawNodes as $key => $rawNode) {

      $image = $this->getImage();
      $rawNode += [
        'field_tags' => [
          ['target_id' => $this->getDemoContentTagId()],
        ],
        'field_teaser_image' => [
          ['target_id' => $image->id()],
        ],
        'field_teaser_text' => [
          $this->generateBlindText(50),
        ],
        'field_teaser_sub_title' => [
          $this->generateBlindText(4),
        ],
      ];

      $node = Node::create($rawNode);
      $node->save();

      if ($key !== 'teaser_page') {
        $this->Ids[] = $node->id();
      }
      else {
        $teaserPage = $node;
      }
    }
    $this->generateParagraphs($teaserPage);
  }

  protected function generateParagraphs(Node $teaserPage) {
    $teasers[] = Paragraph::create([
      'type' => 'node_reference',
      'field_title' => 'Teaser - Long Text',
      'field_sub_title' => $this->generateBlindText(3),
      'field_node_reference_viewmode' => 'long_text',
      'field_node_reference_nodes' => $this->Ids,
    ]);

    $teasers[] = Paragraph::create([
      'type' => 'node_reference',
      'field_title' => 'Teaser - Slim',
      'field_sub_title' => $this->generateBlindText(3),
      'field_node_reference_viewmode' => 'slim',
      'field_node_reference_nodes' => $this->Ids,
    ]);

    $teasers[] = Paragraph::create([
      'type' => 'node_reference',
      'field_title' => 'Teaser - Small Image',
      'field_sub_title' => $this->generateBlindText(3),
      'field_node_reference_viewmode' => 'small_image',
      'field_node_reference_nodes' => $this->Ids,

    ]);

    $teasers[] = Paragraph::create([
      'type' => 'node_reference',
      'field_title' => 'Teaser - Preview',
      'field_sub_title' => $this->generateBlindText(3),
      'field_node_reference_viewmode' => 'preview',
      'field_node_reference_nodes' => $this->Ids,
    ]);

    $teaserPage->set('field_content_paragraphs', $teasers);
    $teaserPage->save();

    return $teasers;
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
