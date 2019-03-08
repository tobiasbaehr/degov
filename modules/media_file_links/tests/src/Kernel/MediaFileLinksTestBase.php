<?php

namespace Drupal\Tests\media_file_links\Kernel;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\KernelTests\KernelTestBase;
use Drupal\media\Entity\Media;
use Drupal\Tests\media\Traits\MediaTypeCreationTrait;

/**
 * Class SuggestionsTest.
 *
 * @package Drupal\Tests\media_file_links\Kernel
 */
class MediaFileLinksTestBase extends KernelTestBase {

  use MediaTypeCreationTrait;

  public static $modules = [
    'media_file_links',
    'field',
    'file',
    'image',
    'media',
    'media_test_source',
    'system',
    'user',
  ];

  protected $supportedMediaId;

  protected $unsupportedMediaId;

  protected $fileIds = [];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installSchema('file', ['file_usage']);

    $this->installEntitySchema('file');
    $this->installEntitySchema('user');
    $this->installEntitySchema('media');

    // Save a document file.
    $pdfFile = file_save_data(file_get_contents(drupal_get_path('module', 'degov_demo_content') . '/fixtures/dummy.pdf'), 'public://dummy.pdf', FILE_EXISTS_REPLACE);
    $this->fileIds['pdf'] = $pdfFile->id();
    $wordFile = file_save_data(file_get_contents(drupal_get_path('module', 'degov_demo_content') . '/fixtures/word-document.docx'), 'public://word-document.docx', FILE_EXISTS_REPLACE);
    $this->fileIds['word'] = $wordFile->id();

    // Create a supported document entity.
    $documentType = $this->createMediaType('test', ['id' => 'document', 'label' => 'Document']);

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
        'target_id' => $this->fileIds['pdf'],
      ],
    ]);
    $newDocument->save();
    $this->supportedMediaId = $newDocument->id();

    // Create an unsupported foo entity.
    $fooType = $this->createMediaType('test', ['id' => 'foo', 'label' => 'Foo']);

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
        'target_id' => $this->fileIds['pdf'],
      ],
    ]);
    $newFoo->save();
    $this->unsupportedMediaId = $newFoo->id();
  }

}
