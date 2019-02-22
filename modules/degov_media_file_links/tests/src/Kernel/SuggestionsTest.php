<?php

namespace Drupal\Tests\degov_media_file_links\Kernel;

/**
 * Class SuggestionsTest.
 *
 * @package Drupal\Tests\degov_media_file_links\Kernel
 */
class SuggestionsTest extends MediaFileLinksTestBase {

  private $fileSuggester;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->fileSuggester = \Drupal::service('degov_media_file_links.file_suggester');
  }

  public function testFindSupportedMediaByTitle(): void {
    $searchResult = $this->fileSuggester->findBySearchString('document');
    self::assertEquals('[{"id":"1","title":"Test document","bundle":"document","mimetype":"application\/pdf"}]', $searchResult);
  }

  public function testUnsupportedMediaCannotBeFoundByTitle(): void {
    $searchResult = $this->fileSuggester->findBySearchString('foo');
    self::assertEquals('[]', $searchResult);
  }

  public function testFindOnlySupportedMediaByFilename(): void {
    $searchResult = $this->fileSuggester->findBySearchString('dummy');
    self::assertEquals('[{"id":"1","title":"Test document","bundle":"document","mimetype":"application\/pdf"}]', $searchResult);

  }
}
