<?php

namespace Drupal\Tests\media_file_links\Kernel;

use Drupal\filter\Entity\FilterFormat;
use Drupal\editor\EditorXssFilter\Standard;

/**
 * Class EditorFilterTest.
 *
 * @package Drupal\Tests\media_file_links\Kernel
 */
class EditorFilterTest extends MediaFileLinksTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installConfig(['filter_test']);
  }

  public function testOldPlaceholderIsBrokenByEditorXssProtection(): void {
    $in = '<a href="[media:file:12]">Link</a>';
    $filterFormat = FilterFormat::load('filtered_html');
    $out = Standard::filterXss($in, $filterFormat);
    self::assertSame('<a href="12]">Link</a>', $out);
  }

  public function testNewPlaceholderIsNotBrokenByEditorXssProtection(): void {
    $in = '<a href="[media/file/12]">Link</a>';
    $filterFormat = FilterFormat::load('filtered_html');
    $out = Standard::filterXss($in, $filterFormat);
    self::assertSame($in, $out);
  }

}
