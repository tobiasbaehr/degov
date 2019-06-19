<?php

namespace Drupal\Tests\media_file_links\Kernel;

/**
 * Class SuggestionsTest.
 *
 * @package Drupal\Tests\media_file_links\Kernel
 */
class SuggestionsTest extends MediaFileLinksTestBase {

  private $fileSuggester;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->fileSuggester = \Drupal::service('media_file_links.file_suggester');
  }

  public function testFindSupportedMediaByTitle(): void {
    $searchResult = $this->fileSuggester->findBySearchString('document');
    self::assertEquals('[{"id":"1","title":"Test document","bundle":"document","bundleLabel":"Document","mimetype":"application\/pdf","iconClass":"fas fa-file-pdf","filename":"dummy.pdf"}]', $searchResult);
  }

  public function testUnsupportedMediaCannotBeFoundByTitle(): void {
    $searchResult = $this->fileSuggester->findBySearchString('foo');
    self::assertEquals('[]', $searchResult);
  }

  public function testFindOnlySupportedMediaByFilename(): void {
    $searchResult = $this->fileSuggester->findBySearchString('dummy');
    self::assertEquals('[{"id":"1","title":"Test document","bundle":"document","bundleLabel":"Document","mimetype":"application\/pdf","iconClass":"fas fa-file-pdf","filename":"dummy.pdf"}]', $searchResult);
  }

  public function testQueryShouldOnlyReturnOneSuggestionPerEntity(): void {
    $searchResult = $this->fileSuggester->findBySearchString('um');
    self::assertEquals('[{"id":"1","title":"Test document","bundle":"document","bundleLabel":"Document","mimetype":"application\/pdf","iconClass":"fas fa-file-pdf","filename":"dummy.pdf"}]', $searchResult);
  }

}
