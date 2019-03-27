<?php

namespace Drupal\Tests\degov_demo_content\Unit;

use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\degov_demo_content\MediaBundle;
use Drupal\field\Entity\FieldConfig;
use Drupal\file\FileInterface;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;


class MediaBundleTest extends UnitTestCase {

  public function testBundleHasField(): void {
    $entityFieldManager = $this->prophesize(EntityFieldManagerInterface::class);
    $entityBundleInfo = $this->prophesize(EntityTypeBundleInfoInterface::class);
    $entityFieldManager->getFieldDefinitions(Argument::type('string'), Argument::type('string'))->willReturn(
      [
        'field_1' => 'value1',
        'field_2' => 'value2',
      ]
    );
    $mediaBundle = new MediaBundle($entityFieldManager->reveal(), $entityBundleInfo->reveal());
    self::assertTrue($mediaBundle->bundleHasField('field_1', 'facts'));
    self::assertFalse($mediaBundle->bundleHasField('field_3', 'facts'));
  }

  public function testBundleExistsInStorage(): void {
    $entityFieldManager = $this->prophesize(EntityFieldManagerInterface::class);
    $entityBundleInfo = $this->prophesize(EntityTypeBundleInfoInterface::class);
    $entityBundleInfo->getBundleInfo(Argument::type('string'))->willReturn([
      'bundle_1' => [],
      'bundle_2' => [],
      'bundle_3' => []
    ]);
    $mediaBundle = new MediaBundle($entityFieldManager->reveal(), $entityBundleInfo->reveal());
    self::assertTrue($mediaBundle->bundleExistsInStorage('bundle_1'));
    self::assertFalse($mediaBundle->bundleExistsInStorage('bundle_77'));
  }

  public function testComputeReferenceFieldArrayWithImageField(): void {
    $entityFieldManager = $this->prophesize(EntityFieldManagerInterface::class);

    $fieldConfig = $this->prophesize(FieldConfig::class);
    $fieldConfig->id()->willReturn('media.image.field_royality_free');
    $fieldConfig->getType()->willReturn('image');

    $fieldDefinitions = [
      'image' => $fieldConfig->reveal()
    ];

    $entityFieldManager->getFieldDefinitions(Argument::type('string'), Argument::type('string'))->willReturn($fieldDefinitions);
    $entityBundleInfo = $this->prophesize(EntityTypeBundleInfoInterface::class);

    $file = $this->prophesize(FileInterface::class);
    $file->id()->willReturn(1);
    $files['image_1'] = $file->reveal();

    $mediaBundle = new MediaBundle($entityFieldManager->reveal(), $entityBundleInfo->reveal());

    $mediaItem = array(
      'bundle' => 'image',
      'name' => 'demo image with a fixed title',
      'file' =>
        array (
          'file_name' => 'vladimir-riabinin-1058013-unsplash.jpg',
          'field_name' => 'image',
        ),
      'field_image_caption' => 'The first image for demonstration purposes',
      'status' => 1,
      'field_include_search' => 1,
    );

    self::assertEquals(
      $mediaBundle->computeReferenceFieldArray($mediaItem, 'image_1', $files),
      array(
        'image' =>
          array (
            'target_id' => 1,
            'alt' => 'demo image with a fixed title',
            'title' => 'demo image with a fixed title',
          ),
      )
    );

  }

  public function testComputeReferenceFieldArrayWithEntityReferenceField(): void {
    $entityFieldManager = $this->prophesize(EntityFieldManagerInterface::class);

    $fieldConfig = $this->prophesize(FieldConfig::class);
    $fieldConfig->id()->willReturn('media.document.field_some');
    $fieldConfig->getType()->willReturn('entity_reference');

    $fieldDefinitions = [
      'field_some_entity_reference' => $fieldConfig->reveal()
    ];

    $entityFieldManager->getFieldDefinitions(Argument::type('string'), Argument::type('string'))->willReturn($fieldDefinitions);
    $entityBundleInfo = $this->prophesize(EntityTypeBundleInfoInterface::class);

    $file = $this->prophesize(FileInterface::class);
    $file->id()->willReturn(1);
    $files['document_1'] = $file->reveal();

    $mediaBundle = new MediaBundle($entityFieldManager->reveal(), $entityBundleInfo->reveal());

    $mediaItem = array(
      'bundle' => 'document',
      'name' => 'demo pdf with a fixed title',
      'file' =>
        array (
          'file_name' => 'document.pdf',
          'field_name' => 'field_document',
        ),
      'field_image_caption' => 'The first pdf for demonstration purposes',
      'status' => 1,
      'field_include_search' => 1,
    );

    self::assertEquals(
      $mediaBundle->computeReferenceFieldArray($mediaItem, 'document_1', $files),
      array(
        'field_document' =>
          array (
            'target_id' => 1,
          ),
      )
    );
  }

}
