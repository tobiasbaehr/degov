<?php

namespace Drupal\Tests\filter_disallow\Kernel;

use Drupal\Core\Messenger\MessengerInterface;
use Drupal\filter_disallow\Plugin\Filter\FilterHtmlDisallow;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\user\Traits\UserCreationTrait;

class FilterTest extends KernelTestBase {

  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'filter_disallow',
    'user',
    'system',
  ];

  private $filter;

  private $textWithUnwantedHtml;

  private $textWithUnwantedHtmlStripped;

  private $cleanText;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installSchema('system', ['sequences']);

    $this->filter = new FilterHtmlDisallow([], '', ['provider' => '']);

    $user = $this->createUser(['view filter_disallow messages']);
    \Drupal::currentUser()->setAccount($user);

    $this->textWithUnwantedHtml = '<p>In this text there is <script>a script tag</script> that should be removed äöüÄÖÜßßß</p>';
    $this->textWithUnwantedHtmlStripped = '<p>In this text there is  that should be removed äöüÄÖÜßßß</p>';
    $this->cleanText = '<p>In this text there is nothing that should be removed äöüÄÖÜßßß</p>';
  }

  public function testFilterTagIsRemoved(): void {
    $filteredText = $this->filter->stripHtmlTag($this->textWithUnwantedHtml, 'script');
    self::assertEquals($this->textWithUnwantedHtmlStripped, $filteredText);
  }

  public function testFilterTagIsNotRemoved(): void {
    $filteredText = $this->filter->stripHtmlTag($this->cleanText, 'script');
    self::assertEquals($this->cleanText, $filteredText);
  }

  public function testMessagesArePostedOnRemoval(): void {
    $this->filter->stripHtmlTag($this->textWithUnwantedHtml, 'script');
    $messages = \Drupal::messenger()
      ->messagesByType(MessengerInterface::TYPE_WARNING);
    self::assertCount(1, $messages);
  }

  public function testMessagesAreNotPostedIfNothingWasFiltered(): void {
    $this->filter->stripHtmlTag($this->cleanText, 'script');
    $messages = \Drupal::messenger()
      ->messagesByType(MessengerInterface::TYPE_WARNING);
    self::assertCount(0, $messages);
  }

}