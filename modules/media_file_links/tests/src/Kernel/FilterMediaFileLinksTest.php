<?php

namespace Drupal\Tests\media_file_links\Kernel;

use Drupal\filter\FilterProcessResult;
use Drupal\media_file_links\Plugin\Filter\FilterMediaFileLinks;

/**
 * Class SuggestionsTest.
 *
 * @package Drupal\Tests\media_file_links\Kernel
 */
class FilterMediaFileLinksTest extends MediaFileLinksTestBase {

  /**
   * Test input filter outputs resolved url.
   */
  public function testInputFilterOutputsResolvedUrl(): void {
    $inputFilter = new FilterMediaFileLinks([], '', ['provider' => '']);
    $formattedText = $inputFilter->process('[media/file/' . $this->supportedMediaId . ']', \Drupal::languageManager()->getCurrentLanguage());
    self::assertInstanceOf(FilterProcessResult::class, $formattedText);
    self::assertContains('dummy.pdf', $formattedText->getProcessedText());
  }

}
