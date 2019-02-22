<?php

namespace Drupal\Tests\degov_media_file_links\Kernel;

/**
 * Class LinkResolutionTestTest.
 *
 * @package Drupal\Tests\degov_media_file_links\Kernel
 */
class LinkResolutionTest extends MediaFileLinksTestBase {

  /**
   * @var \Drupal\degov_media_file_links\Service\MediaFileLinkResolver
   *   $fileLinkResolver
   */
  private $fileLinkResolver;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->fileLinkResolver = \Drupal::service('degov_media_file_links.file_link_resolver');
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
}
