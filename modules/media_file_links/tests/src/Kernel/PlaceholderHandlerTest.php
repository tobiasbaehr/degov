<?php

namespace Drupal\Tests\media_file_links\Kernel;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\TypedData\ComplexDataDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\DataReferenceDefinitionInterface;
use Drupal\link\LinkItemInterface;
use Drupal\media_file_links\Plugin\Field\FieldType\MediaFileLinkItem;
use Drupal\media_file_links\Service\MediaFileLinkPlaceholderHandler;

/**
 * Class PlaceholderHandlerTest.
 *
 * @package Drupal\Tests\media_file_links\Kernel
 */
class PlaceholderHandlerTest extends MediaFileLinksTestBase {

  /**
   * @var \Drupal\media_file_links\Service\MediaFileLinkPlaceholderHandler
   *   $fileLinkResolver
   */
  private $placeholderHandler;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->placeholderHandler = new MediaFileLinkPlaceholderHandler();
  }

  public function testRandomStringIsNoPlaceholder(): void {
    $result = $this->placeholderHandler->isValidMediaFileLinkPlaceholder('Not a placeholder!');
    self::assertEquals(FALSE, $result);
  }

  public function testMenuPlaceholderIsPlaceholder(): void {
    $result = $this->placeholderHandler->isValidMediaFileLinkPlaceholder('Some text here <media/file/1>');
    self::assertEquals(TRUE, $result);
  }

  public function testTextPlaceholderIsPlaceholder(): void {
    $result = $this->placeholderHandler->isValidMediaFileLinkPlaceholder('Some text here [media/file/1]');
    self::assertEquals(TRUE, $result);
  }

  public function testLinkItemMediaIdResolutionSingleDigit(): void {
    $mediaId = $this->placeholderHandler->getMediaIdFromPlaceholder('Some text here <media/file/1>');
    self::assertEquals(1, $mediaId);
  }

  public function testLinkItemMediaIdResolutionDoubleDigit(): void {
    $mediaId = $this->placeholderHandler->getMediaIdFromPlaceholder('Some text here <media/file/12>');
    self::assertEquals(12, $mediaId);
  }

}
