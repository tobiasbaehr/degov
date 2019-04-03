<?php

namespace Drupal\degov_demo_content\Generator;

use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Logger\LoggerChannelFactory;
use Drupal\degov_demo_content\MediaBundle;
use Drupal\media\Entity\Media;
use Symfony\Component\Yaml\Yaml;

/**
 * Class ContentGenerator.
 *
 * @package Drupal\degov_demo_content\Generator
 */
class ContentGenerator {

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * The entity type we are working with.
   *
   * Overridden in the type-specific implementations.
   *
   * @var string
   */
  protected $entityType = '';

  /**
   * Counter for the word generation. Makes generated content more static
   *
   * @var int
   */
  private $counter = 0;

  /**
   * Base string for text generation.
   *
   * @var string
   */
  private const BLINDTEXT = 'Lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat sed diam voluptua At vero eos et accusam et justo duo dolores et ea rebum Stet clita kasd gubergren no sea takimata sanctus est Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat sed diam voluptua At vero eos et accusam et justo duo dolores et ea rebum Stet clita kasd gubergren no sea takimata sanctus est Lorem ipsum dolor sit amet';

  /**
   * @var \Drupal\degov_demo_content\MediaBundle
   */
  protected $mediaBundle;
  /**
   * @var LoggerChannelFactory
   */
  private $loggerChannelFactory;

  public function __construct(ModuleHandler $moduleHandler, EntityTypeManager $entityTypeManager, MediaBundle $mediaBundle, LoggerChannelFactory $loggerChannelFactory) {
    $this->moduleHandler = $moduleHandler;
    $this->entityTypeManager = $entityTypeManager;
    $this->mediaBundle = $mediaBundle;
    $this->loggerChannelFactory = $loggerChannelFactory;
  }

  /**
   * Looks for a file and reads the date stored within.
   */
  public function loadDefinitions(string $definitions_file_name): ?array {
    $definitions_file_path = $this->moduleHandler->getModule('degov_demo_content')
        ->getPath() . '/entity_definitions/' . $definitions_file_name;
    if (file_exists($definitions_file_path) && is_file($definitions_file_path) && is_readable($definitions_file_path)) {
      return Yaml::parseFile($definitions_file_path);
    }

    throw new \Exception('Could not read definitions file from path ' . $definitions_file_path);
  }

  /**
   * @return int|null|string
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function getDemoContentTagId() {
    $tag_term = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties([
      'name' => DEGOV_DEMO_CONTENT_TAG_NAME,
      'vid' => DEGOV_DEMO_CONTENT_TAGS_VOCABULARY_NAME,
    ]);
    if (!empty($tag_term)) {
      $tag_term = reset($tag_term);
      return $tag_term->id();
    }
    return NULL;
  }

  /**
   * @return int|null|string
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function getDemoContentCopyrightId() {
    $tag_term = $this->entityTypeManager->getStorage('taxonomy_term')->loadByProperties([
      'name' => DEGOV_DEMO_CONTENT_TAG_NAME,
      'vid' => DEGOV_DEMO_CONTENT_COPYRIGHT_VOCABULARY_NAME,
    ]);
    if (!empty($tag_term)) {
      $tag_term = reset($tag_term);
      return $tag_term->id();
    }
    return NULL;
  }

  /**
   * Deletes the generated entities.
   */
  public function deleteContent(): void {
    if ($this->getDemoContentTagId() === NULL) {
      return;
    }
    $entities = \Drupal::entityTypeManager()
      ->getStorage($this->entityType)
      ->loadByProperties([
        'field_tags' => $this->getDemoContentTagId(),
      ]);

    foreach ($entities as $entity) {
      $entity->delete();
    }
  }

  /**
   * @param array $rawElement
   * @param bool $resolveReferences
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function prepareValues(array &$rawElement, bool $resolveReferences = TRUE): void {
    foreach ($rawElement as $index => &$value) {
      if(\is_string($value)) {
        $this->replaceValues($rawElement, $value, $index, $resolveReferences);
      } else {
        if(\is_array($value)) {
          $this->prepareValues($rawElement[$index], $resolveReferences);
        }
      }
    }
  }

  /**
   * @param array $rawElement
   * @param $value
   * @param string $index
   * @param bool $resolveReferences
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  private function replaceValues(array &$rawElement, $value, string $index, bool $resolveReferences = TRUE): void {
    if($value === '{{DEMOTAG}}') {
      $rawElement[$index] = ['target_id' => $this->getDemoContentTagId()];
      return;
    }

    if ($resolveReferences && preg_match('/^\{\{MEDIA_ID\_([a-zA-Z\_]*)\}\}$/', $value, $mediaBundle)) {
      $mediaBundle = strtolower($mediaBundle[1]);
      if (!$this->hasBundle($mediaBundle)) {
        return;
      }
      $mediaId = $this->getMedia($mediaBundle)->id();
      $rawElement[$index] = [
        'target_id' => $mediaId,
      ];
      return;
    }

    while(strpos($value, '{{SUBTITLE}}') !== FALSE) {
      $value = preg_replace('/\{\{SUBTITLE\}\}/', $this->generateBlindText(5), $value, 1);
    }

    while(strpos($value, '{{TEXT}}') !== FALSE) {
      $value = preg_replace('/\{\{TEXT\}\}/', $this->generateBlindText(50), $value, 1);
    }

    if($resolveReferences) {
      while(preg_match('/\{\{MEDIA_ID\_([a-zA-Z^_]*)_ENTITY_EMBED\}\}/', $value, $mediaBundle)) {
        $mediaBundle = strtolower($mediaBundle[1]);
        if (!$this->hasBundle($mediaBundle)) {
          continue;
        }
        $mediaUuid = $this->getMedia($mediaBundle)->uuid();
        $embed_string = sprintf('<drupal-entity alt="Miniaturbild" data-embed-button="media_browser" data-entity-embed-display="media_image" data-entity-embed-display-settings="{&quot;image_style&quot;:&quot;crop_2_to_1&quot;,&quot;image_link&quot;:&quot;&quot;}" data-entity-type="media" data-entity-uuid="%s" title="sadipscing elitr sed diam nonumy"></drupal-entity>', $mediaUuid);
        $value = preg_replace('/\{\{MEDIA_ID\_([a-zA-Z^_]*)_ENTITY_EMBED\}\}/', $embed_string, $value, 1);
      }
    }

    $rawElement[$index] = $value;
  }

  /**
   * @param string $bundle
   *
   * @return array
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function getMedias(string $bundle): array {
    if (!$this->mediaBundle->bundleHasField('field_tags', $bundle)) {
      throw new \Exception('Found media without field_tags. Media needs this field, otherwise demo content cannot be reset.');
    }

    $mediaIds = \Drupal::entityQuery('media')
      ->condition('bundle', $bundle)
      ->condition('field_tags', $this->getDemoContentTagId());

    if (\count($mediaIds = $mediaIds->execute()) === '0') {
      throw new \Exception('Could not retrieve any media ids.');
    }

    return $mediaIds;
  }

  protected function getMedia(string $bundle): Media {
    $medias = $this->getMedias($bundle);
    $this->counter++;
    try {
      $index = $this->counter % \count($medias);
    } catch(\DivisionByZeroError $exception) {
      throw new \Exception('Media is missing. Maybe the field definitions in your demo content are wrong?');
    }
    $keys = array_keys($medias);

    return Media::load($medias[$keys[$index]]);
  }

  /**
   * @param int $wordCount
   *
   * @return string
   */
  public function generateBlindText(int $wordCount): string {
    $phrase = [];
    for ($i = 0; $i < $wordCount; $i++) {
      $phrase[] = $this->getWord();
    }
    return implode(' ', $phrase);
  }

  /**
   * @return string
   */
  protected function getWord(): string {
    $words = explode(' ', self::BLINDTEXT);
    $this->counter++;
    $index = $this->counter % count($words);
    return $words[$index];
  }

  /**
   * @param string $defName
   * @param $tag
   *
   * @return mixed
   * @throws \Exception
   */
  protected function loadDefinitionByNameTag(string $defName, $tag) {
    $def = $this->loadDefinitions($defName . '.yml');
    return $def[$tag];
  }

  /**
   * Loads a specific definition by type.
   *
   * @param string $defName
   *   The definition file name.
   * @param string $type
   *   The type of definition to load.
   *
   * @return array
   *   The filtered definitions.
   * @throws \Exception
   */
  protected function loadDefinitionByNameType(string $defName, string $type): array {
    $def = $this->loadDefinitions($defName . '.yml');
    return array_filter($def, function ($var) use ($type) {
      return $var['type'] === $type;
    });
  }

  protected function hasBundle(string $bundle) {
    if (!$this->mediaBundle->bundleExistsInStorage($bundle)) {
      $this->loggerChannelFactory->get('degov_demo_content')
        ->notice(
          sprintf(
            'Bundle named %s does not exist in storage. You might want to install an additional module for it. Therefor bypassing content creation for that bundle.',
            $bundle
          )
        );

      return FALSE;
    }

    return TRUE;
  }

}
