<?php

namespace Drupal\Tests\media_file_links\Kernel;

use Drupal\media\Entity\Media;

/**
 * Class LinkResolutionTestTest.
 *
 * @package Drupal\Tests\media_file_links\Kernel
 */
class LinkResolutionTest extends MediaFileLinksTestBase {

  /**
   * @var \Drupal\media_file_links\Service\MediaFileLinkResolver
   *   $fileLinkResolver
   */
  private $fileLinkResolver;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->fileLinkResolver = \Drupal::service('media_file_links.file_link_resolver');
  }

  public function testLinkResolutionWithExistingSupportedMedia(): void {
    $urlString = $this->fileLinkResolver->getFileUrlString($this->supportedMediaId);
    self::assertContains('dummy.pdf', $urlString);
  }

  public function testLinkResolutionWithExistingUnsupportedMedia(): void {
    $urlString = $this->fileLinkResolver->getFileUrlString($this->unsupportedMediaId);
    self::assertContains('', $urlString);
  }

  public function testLinkResolutionWithNonexistentMedia(): void {
    $urlString = $this->fileLinkResolver->getFileUrlString(99);
    self::assertEquals('', $urlString);
  }

  public function testChangingTheFileResultsInUpdatedLink(): void {
    $media = Media::load($this->supportedMediaId);
    $media->set('field_document', [
      'target_id' => $this->fileIds['word'],
    ]);
    $media->save();

    $urlString = $this->fileLinkResolver->getFileUrlString($this->supportedMediaId);
    self::assertContains('word-document.docx', $urlString);
  }

}