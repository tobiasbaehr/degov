<?php

namespace Drupal\degov_demo_content\Generator;

use Drupal\block\Entity\Block;
use Drupal\block_content\Entity\BlockContent;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandler;

/**
 * Class BlockContentGenerator.
 *
 * @package Drupal\degov_demo_content\Generator
 */
class BlockContentGenerator extends ContentGenerator implements GeneratorInterface {

  /**
   * Node title which contains slideshow paragraph.
   */
  const NODE_WITH_SLIDER_LABEL = "Page with slideshow";

  /**
   * The name of field which relates to the Paragraph in the node.
   */
  const NODE_PARAGRAPH_FIELD = 'field_header_paragraphs';

  /**
   * The name of field which relates to the Paragraph in the block.
   */
  const BLOCK_PARAGRAPH_FIELD = 'field_content_paragraphs';

  /**
   * The type of entity.
   *
   * @var string
   */
  protected $entityType;

  /**
   * The type of block.
   *
   * @var string
   */
  protected $blockType;

  /**
   * BlockContentGenerator constructor.
   *
   * {@inheritDoc}
   */
  public function __construct(ModuleHandler $module_handler, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($module_handler, $entity_type_manager);
    $this->entityType = 'block_content';
    $this->blockType = 'basic';
  }

  /**
   * {@inheritdoc}
   */
  public function generateContent(): void {
    foreach ($this->loadDefinitions('block_content.yml') as $definition) {
      $block = $this->createContentBlock($definition);
      $this->generateBlockReferenceParagraphs($block);
    }
    $this->enableParagraphField();
  }

  /**
   * Creates the ContentBlock entity.
   *
   * @param array $definition
   *   Definition array.
   *
   * @return \Drupal\block_content\Entity\BlockContent|\Drupal\Core\Entity\EntityInterface
   *   Content block.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function createContentBlock(array $definition) {
    $block = BlockContent::create($definition);
    $block->save();
    $this->setBlockToRegion($block);

    return $block;
  }

  /**
   * Place block to the region.
   *
   * @param \Drupal\block_content\Entity\BlockContent $block
   *   The Block entity.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function setBlockToRegion(BlockContent $block) {
    $placed_block = Block::create([
      'id' => 'slideshow',
      'theme' => 'degov_theme',
      'weight' => -50,
      'status' => TRUE,
      'region' => 'content',
      'plugin' => 'block_content:' . $block->uuid(),
      'settings' => [
        'id' => 'block_content:' . $block->uuid(),
        'label' => 'Slideshow',
        'provider' => 'block_content',
        'label_display' => '0',
        'status' => TRUE,
        'info' => '',
        'view_mode' => 'full',
      ],
      'visibility' => [
        'request_path' => [
          'id' => 'request_path',
          'negate' => FALSE,
          'pages' => '<front>',
        ],
      ],
    ]);

    $placed_block->save();
  }

  /**
   * Generates reference to paragraph in the block field.
   *
   * @param \Drupal\block_content\Entity\BlockContent $block_content
   *   BlockContent entity.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function generateBlockReferenceParagraphs(BlockContent $block_content): void {
    $nodes_array = $this->entityTypeManager->getStorage('node')
      ->loadByProperties([
        'type' => 'normal_page',
        'title' => self::NODE_WITH_SLIDER_LABEL,
      ]);

    /** @var \Drupal\node\Entity\Node $node */
    if ($node = reset($nodes_array)) {
      /** @var \Drupal\paragraphs\Entity\Paragraph $paragraph_slideshow */
      $paragraph_slideshow = $node->get(self::NODE_PARAGRAPH_FIELD)->entity;

      $block_content->set(self::BLOCK_PARAGRAPH_FIELD, $paragraph_slideshow);
      $block_content->save();
    }
  }

  /**
   * Enable paragraph fields on view display & form display.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function enableParagraphField() {
    $this->enableParagraphFieldOnViewDisplay();
    $this->enableParagraphFieldOnFormDisplay();
  }

  /**
   * Enable paragraph field on view display.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function enableParagraphFieldOnViewDisplay() {
    /** @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface $view_display */
    $view_display = $this->entityTypeManager
      ->getStorage('entity_view_display')
      ->load("{$this->entityType}.{$this->blockType}.default");
    $view_display->setComponent(self::BLOCK_PARAGRAPH_FIELD, ['label' => 'hidden']);
    $view_display->save();
  }

  /**
   * Returns array of paragraph field for the form display.
   *
   * @return array
   *   Settings array.
   */
  private function getParagraphFieldFormDisplaySettings() {
    return [
      'type' => 'paragraphs',
      'weight' => 4,
      'region' => "content",
      'settings' => [
        'edit_mode' => 'closed',
        'closed_mode' => 'summary',
        'autocollapse' => 'none',
        'closed_mode_threshold' => 0,
        'add_mode' => 'modal',
        'form_display_mode' => 'default',
        'default_paragraph_type' => '_none',
        'features' => [
          'duplicate' => 'duplicate',
          'collapse_edit_all' => 'collapse_edit_all',
          'add_above' => 'add_above',
        ],
      ],
      'third_party_settings' => [],
    ];
  }

  /**
   * Enable paragraph field on the form display.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function enableParagraphFieldOnFormDisplay() {
    /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $form_display */
    $form_display = $this->entityTypeManager
      ->getStorage('entity_form_display')
      ->load("{$this->entityType}.{$this->blockType}.default");

    $form_display->setComponent(self::BLOCK_PARAGRAPH_FIELD,
      $this->getParagraphFieldFormDisplaySettings()
    );
    $form_display->save();
  }

  /**
   * Deletes the generated block.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function deleteContent(): void {
    $entities = $this->entityTypeManager
      ->getStorage($this->entityType)
      ->loadMultiple();

    foreach ($entities as $entity) {
      $entity->delete();
    }
  }

  /**
   * Regenerates the block.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function resetContent(): void {
    $this->deleteContent();
    $this->generateContent();
  }

}
