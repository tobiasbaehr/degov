<?php

namespace Drupal\Tests\media_file_links\Kernel;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\TypedData\ComplexDataDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataReferenceDefinitionInterface;
use Drupal\link\LinkItemInterface;
use Drupal\media_file_links\Plugin\Field\FieldType\MediaFileLinkItem;

/**
 * Class MediaFileLinkItemTest.
 *
 * @package Drupal\Tests\media_file_links\Kernel
 */
class MediaFileLinkItemTest extends MediaFileLinksTestBase {

  /**
   * @var \Drupal\media_file_links\Service\MediaFileLinkResolver
   *   $fileLinkResolver
   */
  private $fileLinkResolver;

  private $mediaFileLinkItem;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $property_definition = $this->getMock(DataReferenceDefinitionInterface::class);
    $data_definition = $this->getMock(ComplexDataDefinitionInterface::class);
    $data_definition->expects($this->any())
      ->method('getPropertyDefinitions')
      ->willReturn([$property_definition]);

    $this->mediaFileLinkItem = new MediaFileLinkItem($data_definition, null, null);
  }

  public function testLinkItemMediaIdResolutionSingleDigit(): void {
    $mediaId = $this->mediaFileLinkItem->getMediaIdFromMediaFilePattern('<media:file:1>');
    self::assertEquals(1, $mediaId);
  }

  public function testLinkItemMediaIdResolutionDoubleDigit(): void {
    $mediaId = $this->mediaFileLinkItem->getMediaIdFromMediaFilePattern('<media:file:12>');
    self::assertEquals(12, $mediaId);
  }

  public function testLinkResolutionWithExistingSupportedMedia(): void {
    $this->mediaFileLinkItem->uri = '<media:file:' . $this->supportedMediaId . '>';
    $urlString = $this->mediaFileLinkItem->getUrl()->toString();
    self::assertContains('dummy.pdf', $urlString);
  }

  public function testLinkResolutionWithExistingUnupportedMedia(): void {
    $this->mediaFileLinkItem->uri = '<media:file:' . $this->unsupportedMediaId . '>';
    $urlString = $this->mediaFileLinkItem->getUrl()->toString();
    self::assertContains('', $urlString);
  }

  public function testLinkResolutionWithNonexistentMedia(): void {
    $this->mediaFileLinkItem->uri = '<media:file:999>';
    $urlString = $this->mediaFileLinkItem->getUrl()->toString();
    self::assertContains('', $urlString);
  }

  public function testLinkResolutionWithRegularUrl(): void {
    $this->mediaFileLinkItem->uri = 'http://www.drupal.org/';
    $urlString = $this->mediaFileLinkItem->getUrl()->toString();
    self::assertContains('http://www.drupal.org/', $urlString);
  }
}
