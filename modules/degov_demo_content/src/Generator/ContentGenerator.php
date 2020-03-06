<?php

namespace Drupal\degov_demo_content\Generator;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\media\Entity\Media;
use Symfony\Component\Yaml\Yaml;
use Drupal\Core\Entity\EntityInterface;

/**
 * Class ContentGenerator.
 *
 * @package \Drupal\degov_demo_content\Generator
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
   * Counter for the word generation. Makes generated content more static.
   *
   * @var int
   */
  private $wordCounter = 0;

  /**
   * Counter for media generation. Makes generated content more static.
   *
   * @var int
   */
  private $mediaCounter = 0;

  /**
   * Base string for text generation.
   *
   * @var string
   */
  private const BLINDTEXT = 'Lorem ipsum äöü ÄÖÜß àéîøū dolor sit amet consetetur äöüÄ ÖÜßà éîøū sadipscing elitr sed diam äöüÄÖ Üßàé îøū nonumy eirmod tempor invidunt äöüÄÖÜ ßàéîøū ut labore et dolore magna aliquyam erat sed ä öüÄ ÖÜ ßàé îøū diam voluptua At vero eos et accusam et justo duo äö üÄÖÜ ßàéî øū dolores et ea rebum Stet clita kasd gubergren no sea äöüÄ ÖÜßàé îøū takimata sanctus est Lorem ipsum dolor sit amet Lorem ipsum dolor sit amet äö üÄÖ Üßàé îøū consetetur sadipscing elitr sed ä ö ü Ä Ö Ü ß à é î ø ū diam nonumy eirmod tempor invidunt ut labore et äöü ÄÖÜß àéîøū dolore magna aliquyam erat sed diam äöü ÄÖÜß àéîøū voluptua At vero eos et accusam äöü ÄÖÜß àéîøū et justo duo dolores et ea äöü ÄÖÜß àéîøū rebum Stet clita kasd gubergren äöü ÄÖÜß àéîøū no sea takimata sanctus est äöü ÄÖÜß àéîøū Lorem ipsum dolor sit amet';

  /**
   * Constructs a new ContentGenerator instance.
   *
   * @param \Drupal\Core\Extension\ModuleHandler $moduleHandler
   *   Module handler.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager.
   */
  public function __construct(ModuleHandler $moduleHandler, EntityTypeManagerInterface $entityTypeManager) {
    $this->moduleHandler = $moduleHandler;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Looks for a file and reads the date stored within.
   *
   * @param string $definitions_file_name
   *   Basename of yaml file in entity_definitions.
   *
   * @return array
   *   Definitions from Yaml.
   *
   * @throws \Exception
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
   * Get demo content tag ID.
   *
   * @return int|null|string
   *   Demo content tag ID.
   *
   * @throws \Exception
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
   * Get demo content copyright ID.
   *
   * @return int|null|string
   *   Demo content copyright ID.
   *
   * @throws \Exception
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
   *
   * @throws \Exception
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
    $paragraphs = $this->entityTypeManager->getStorage('paragraph')->loadMultiple();
    /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
    foreach ($paragraphs as $paragraph) {
      $paragraph->delete();
    }
  }

  /**
   * Generate a timestamp for content created.
   *
   * We build non changing dates for content created based on the
   * DEGOV_DEMO_CONTENT_CREATED_TIMESTAMP and a checksum value based on items
   * unique identifyer.
   *
   * @param string $srcId
   *   Unique ID for the Item form definitions.
   *
   * @return int
   *   Timestamp for ceated.
   */
  public static function getCreatedTimestamp(string $srcId) {
    // Generate a Unique number with 5 digits which is about a day in Unix time (86400s->1 day)
    // Our "latest content" has a "created" date defined in entity_definitions/node.yml
    // A manual date should be  > DEGOV_DEMO_CONTENT_CREATED_TIMESTAMP + 100.000 to be stable.
    return DEGOV_DEMO_CONTENT_CREATED_TIMESTAMP + intval(substr(strval(crc32($srcId)), 0, 5));
  }

  /**
   * Prepare values.
   *
   * @param array $rawElement
   *   Raw element.
   * @param bool $resolveReferences
   *   Resolve references.
   *
   * @throws \Exception
   */
  protected function prepareValues(array &$rawElement, bool $resolveReferences = TRUE): void {
    foreach ($rawElement as $index => &$value) {
      if (is_string($value)) {
        $this->replaceValues($rawElement, $value, $index, $resolveReferences);
      }
      else {
        if (is_array($value)) {
          $this->prepareValues($rawElement[$index], $resolveReferences);
        }
      }
    }
  }

  /**
   * Replace values.
   *
   * @param array $rawElement
   *   Raw element.
   * @param string $value
   *   Value.
   * @param string $index
   *   Index.
   * @param bool $resolveReferences
   *   Resolve references.
   *
   * @throws \Exception
   */
  private function replaceValues(array &$rawElement, $value, string $index, bool $resolveReferences = TRUE): void {

    if ($value === '{{DEMOTAG}}') {
      $rawElement[$index] = ['target_id' => $this->getDemoContentTagId()];
      return;
    }

    // Any Media References.
    if ($resolveReferences && strpos($value, 'MEDIA_ID') !== FALSE) {

      // Images.
      $mediaProperties = [];
      // @see https://regex101.com/r/mA6cA0/9
      if (preg_match('/^{{MEDIA_ID_(?\'bundleId\'[A-Z_]*)(:?-?)(?\'sourceId\'[a-z0-9_]*)?}}$/', $value, $mediaProperties)) {
        $bundleId = strtolower($mediaProperties['bundleId']);

        if ($bundleId === 'image') {
          if (empty($mediaProperties['sourceId'])) {
            throw new \Exception('Media image references must contain a specific image reference. e.g MEDIA_ID_IMAGE-image_1');
          }
          $value = $this->getImageMedia($mediaProperties['sourceId'])->id();
        }
        elseif ($bundleId === 'address' && !empty($mediaProperties['sourceId'])) {
          $value = $this->getMediaBySourceId($mediaProperties['sourceId'])->id();
        }
        else {
          $value = $this->getMedia($bundleId)->id();
        }
        $rawElement[$index] = ['target_id' => $value];
        return;
      }

      // Entity embed.
      // @see https://regex101.com/r/mA6cA0/11
      $mediaProperties = [];
      while (preg_match('/{{MEDIA_ID_(?\'bundleId\'[A-Z_]*)_ENTITY_EMBED(:?-?)(?\'sourceId\'[a-z0-9_]*)?}}/', $value, $mediaProperties)) {
        $bundleId = strtolower($mediaProperties['bundleId']);
        if ($bundleId === 'image') {
          if (empty($mediaProperties['sourceId'])) {
            throw new \Exception('Media image references must contain a specific image reference. e.g MEDIA_ID_IMAGE-image_1');
          }
          $mediaUuid = $this->getImageMedia($mediaProperties['sourceId'])->uuid();
          $replace = '/{{MEDIA_ID_([a-zA-Z^_]*)_ENTITY_EMBED-' . $mediaProperties['sourceId'] . '}}/';
        }
        else {
          $mediaUuid = $this->getImageMedia($bundleId)->uuid();
          $replace = '/{{MEDIA_ID_([a-zA-Z^_]*)_ENTITY_EMBED}}/';
        }
        $embed_string = sprintf('<drupal-entity alt="Miniaturbild" data-embed-button="media_browser" data-entity-embed-display="media_image" data-entity-embed-display-settings="{&quot;image_style&quot;:&quot;crop_2_to_1&quot;,&quot;image_link&quot;:&quot;&quot;}" data-entity-type="media" data-entity-uuid="%s" title="sadipscing elitr sed diam nonumy"></drupal-entity>', $mediaUuid);
        $value = preg_replace($replace, $embed_string, $value, 1);
      }
    }

    while (strpos($value, '{{SUBTITLE}}') !== FALSE) {
      $value = preg_replace('/{{SUBTITLE}}/', $this->generateBlindText(5), $value, 1);
    }

    while (strpos($value, '{{TEXT_PLAIN}}') !== FALSE) {
      $value = preg_replace('/{{TEXT_PLAIN}}/', $this->generateBlindText(50), $value, 1);
    }

    while (strpos($value, '{{TEXT}}') !== FALSE) {
      $value = preg_replace('/{{TEXT}}/', $this->generateBlindText(50, TRUE), $value, 1);
    }

    $rawElement[$index] = $value;
  }

  /**
   * Get media-entity by sourceId.
   *
   * @param string $sourceId
   *   Key from yml definition (media.yml). Example: document_2
   *
   * @throws \Exception
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   Media id of this entity.
   */
  private function getMediaBySourceId(string $sourceId) {
    $def = $this->loadDefinitions('media.yml');
    $bundle = $def[$sourceId]['bundle'];
    $name = $def[$sourceId]['name'];
    $mid = \Drupal::service('entity.query')
      ->get('media')
      ->condition('bundle', $bundle)
      ->condition('name', $name)
      ->execute();

    if (count($mid) > 1) {
      throw new \Exception('Media entity names for demo content must be unique. This name is not unique: ' . $name);
    }
    if (count($mid) < 1) {
      throw new \Exception('Could not get media by name: ' . $name);
    }
    return Media::load(array_pop($mid));
  }

  /**
   * Get media-image entity.
   *
   * @param string $sourceId
   *   Key from yml definition.
   *
   * @throws \Exception
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   Media id of this image.
   */
  private function getImageMedia($sourceId) {
    $def = $this->loadDefinitions('media.yml');
    $imageDef = array_filter($def, function ($var) {
      return $var['bundle'] === 'image';
    });
    $title = $imageDef[$sourceId]['name'];
    $mid = \Drupal::service('entity.query')
      ->get('media')
      ->condition('bundle', 'image')
      ->condition('name', $title)
      ->execute();

    if (count($mid) > 1) {
      throw new \Exception('Media Image titles for demo content must be unique. This title is not unique: ' . $title);
    }
    if (count($mid) < 1) {
      throw new \Exception('Could not get media by title: ' . $title);
    }
    return Media::load(array_pop($mid));
  }

  /**
   * Get medias.
   *
   * @param string $bundle
   *   Bundle.
   *
   * @return array
   *   Medias.
   *
   * @throws \Exception
   */
  protected function getMedias(string $bundle): array {
    $mediaIds = \Drupal::entityQuery('media')
      ->condition('bundle', $bundle)
      ->condition('field_tags', $this->getDemoContentTagId())->execute();
    return $mediaIds;
  }

  /**
   * Get media.
   *
   * @param string $bundle
   *   Bundle.
   *
   * @throws \Exception
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   Media Entity
   */
  protected function getMedia(string $bundle): EntityInterface {
    $medias = $this->getMedias($bundle);
    $this->mediaCounter++;
    try {
      $index = $this->mediaCounter % \count($medias);
    }
    catch (\DivisionByZeroError $exception) {
      throw new \Exception('Media is in ' . $bundle . ' missing. Maybe the field definitions in your demo content are wrong?');
    }
    $keys = array_keys($medias);
    return Media::load($medias[$keys[$index]]);
  }

  /**
   * Generate blind text.
   *
   * @param int $wordCount
   *   Word count.
   * @param bool $addLinks
   *   Add links.
   *
   * @return string
   *   Generated text.
   */
  public function generateBlindText(int $wordCount, bool $addLinks = FALSE): string {
    $this->counter = 0;
    $phrase = [];
    for ($i = 0; $i < $wordCount; $i++) {
      $word = $this->getWord();
      if ($addLinks && $i !== 0 && $i % 5 === 0) {
        $word = '<a href="/">' . $word . '</a>';
      }
      $phrase[] = $word;
    }
    return implode(' ', $phrase);
  }

  /**
   * Get word.
   *
   * @return string
   *   Word.
   */
  protected function getWord(): string {
    $words = explode(' ', self::BLINDTEXT);
    $this->wordCounter++;
    $index = $this->wordCounter % count($words);
    return $words[$index];
  }

  /**
   * Load definition by name tag.
   *
   * @param string $defName
   *   Def name.
   * @param string $tag
   *   Tag.
   *
   * @return array|null
   *   Loaded definition..
   *
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
   *
   * @throws \Exception
   */
  protected function loadDefinitionByNameType(string $defName, string $type): array {
    $def = $this->loadDefinitions($defName . '.yml');
    return array_filter($def, function ($var) use ($type) {
      return $var['type'] === $type;
    });
  }

}
