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

  private $specialChars;

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
    $this->textWithUnwantedHtmlStripped = '<p>In this text there is a script tag that should be removed äöüÄÖÜßßß</p>';
    $this->cleanText = '<p>In this text there is nothing that should be removed äöüÄÖÜßßß</p>';
    $this->specialChars = '<p> ^ ° ¬ ! ¹ ¡ " ² ⅛ § ³ £ $ ¼ ¤ % ½ ⅜ & ¬ ⅝ / { ⅞ ( [ ™ ) ] = } ° ? \ ¿ ` ¸ ¸ @ Ω ł Ł € ţ Ţ ← ¥ ↓ ↑ î Î ø Ø þ Þ ¨ + * ~ â Â ş Ş ð Ð đ ª ŋ Ŋ ħ Ħ ̣ ˙ ĸ & ˝ ˝ ă Ă # \' ` < > | » › « ‹ ¢ © „ ‚ “ ‘ ” ’ µ º , ; · × … ÷ - _ – — </p>';
  }

  public function testFilterTagIsRemoved(): void {
    $filteredText = $this->filter->stripHtmlTag($this->textWithUnwantedHtml, 'script');
    self::assertSame($this->textWithUnwantedHtmlStripped, $filteredText);
  }

  public function testFilterTagIsNotRemoved(): void {
    $filteredText = $this->filter->stripHtmlTag($this->cleanText, 'script');
    self::assertSame($this->cleanText, $filteredText);
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

  public function testSpecialChars(): void {
    $filteredText = $this->filter->stripHtmlTag($this->specialChars, 'script');
    self::assertSame($this->specialChars, $filteredText);
  }
}
