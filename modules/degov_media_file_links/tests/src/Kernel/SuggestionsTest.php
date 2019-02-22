<?php

namespace Drupal\Tests\degov_media_file_links\Kernel;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\media\Entity\Media;
use Drupal\Tests\media\Traits\MediaTypeCreationTrait;

/**
 * Class SuggestionsTest.
 *
 * @package Drupal\Tests\degov_media_file_links\Kernel
 */
class SuggestionsTest extends KernelTestBase {

  use MediaTypeCreationTrait;

  public static $modules = [
    'degov_media_file_links',
    'field',
    'file',
    'image',
    'media',
    'media_test_source',
    'system',
    'user',
  ];

  private $fileSuggester;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->fileSuggester = \Drupal::service('degov_media_file_links.file_suggester');

    $this->installSchema('file', ['file_usage']);

    $this->installEntitySchema('file');
    $this->installEntitySchema('user');
    $this->installEntitySchema('media');

    // Save a document file
    $file = file_save_data(file_get_contents(drupal_get_path('module', 'degov_demo_content') . '/fixtures/dummy.pdf'), 'public://dummy.pdf', FILE_EXISTS_REPLACE);

    // Create a supported document entity
    $documentType = $this->createMediaType('test', ['id' => 'document']);

    $fieldDocumentStorage = FieldStorageConfig::create([
      'entity_type' => 'media',
      'field_name'  => 'field_document',
      'type'        => 'file',
    ]);
    $fieldDocumentStorage->save();

    FieldConfig::create([
      'field_storage' => $fieldDocumentStorage,
      'bundle'        => $documentType->id(),
      'label'         => 'Document field',
    ])->save();

    $newDocument = Media::create([
      'bundle'         => $documentType->id(),
      'name'           => 'Test document',
      'field_document' => [
        'target_id' => $file->id(),
      ],
    ]);
    $newDocument->save();
    $this->supportedMediaId = $newDocument->id();

    // Create an unsupported foo entity
    $fooType = $this->createMediaType('test', ['id' => 'foo']);

    $fieldFooStorage = FieldStorageConfig::create([
      'entity_type' => 'media',
      'field_name'  => 'field_foo',
      'type'        => 'file',
    ]);
    $fieldFooStorage->save();

    FieldConfig::create([
      'field_storage' => $fieldFooStorage,
      'bundle'        => $fooType->id(),
      'label'         => 'Foo field',
    ])->save();

    $newFoo = Media::create([
      'bundle'    => $fooType->id(),
      'name'      => 'Test foo',
      'field_foo' => [
        'target_id' => $file->id(),
      ],
    ]);
    $newFoo->save();
    $this->unsupportedMediaId = $newFoo->id();
  }

  public function testFindSupportedMediaByTitle(): void {
    $searchResult = $this->fileSuggester->findBySearchString('document');
    self::assertEquals(
      [
        [
      'id' => '1',
      'title' => 'Test document',
      'bundle' => 'document',
      'mimetype' => 'application/pdf',
        ]
      ], $searchResult);
  }

  public function testUnsupportedMediaCannotBeFoundByTitle(): void {
    $searchResult = $this->fileSuggester->findBySearchString('foo');
    self::assertEquals([], $searchResult);
  }

  public function testFindOnlySupportedMediaByFilename(): void {
    $searchResult = $this->fileSuggester->findBySearchString('dummy');
    self::assertEquals(
      [
        [
          'id' => '1',
          'title' => 'Test document',
          'bundle' => 'document',
          'mimetype' => 'application/pdf',
        ]
      ], $searchResult);

  }
}
