<?php

use Drupal\degov_theming\TwigExtension\IsNotEmptyValueExtension;
use Drupal\Tests\UnitTestCase;

class EmptyValueCheckerExtensionTest extends UnitTestCase {

  /**
   * @var Drupal\degov_theming\TwigExtension\IsNotEmptyValueExtension
   */
  protected $extension;

  /**
   * {@inheritDoc}
   */
  protected function setUp() {
    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = $this->getMockBuilder('Drupal\Core\Render\RendererInterface')
      ->disableOriginalConstructor()
      ->getMock();
    /** @var \Drupal\Core\Routing\UrlGeneratorInterface $url_generator */
    $url_generator = $this->getMockBuilder('Drupal\Core\Routing\UrlGeneratorInterface')
      ->disableOriginalConstructor()
      ->getMock();
    /** @var \Drupal\Core\Theme\ThemeManagerInterface $theme_manager */
    $theme_manager = $this->getMockBuilder('Drupal\Core\Theme\ThemeManagerInterface')
      ->disableOriginalConstructor()
      ->getMock();
    /** @var \Drupal\Core\Datetime\DateFormatterInterface $date_formatter */
    $date_formatter = $this->getMockBuilder('Drupal\Core\Datetime\DateFormatterInterface')
      ->disableOriginalConstructor()
      ->getMock();
    /** @var \Drupal\Core\Logger\LoggerChannelFactory $loggerFactory */
    $loggerFactory = $this->getMockBuilder('Drupal\Core\Logger\LoggerChannelFactory')
      ->disableOriginalConstructor()
      ->getMock();
    $this->extension = new IsNotEmptyValueExtension($renderer, $url_generator, $theme_manager, $date_formatter, $loggerFactory);
  }

  /**
   * @dataProvider providerTestNotEmpty
   */
  public function testIsNotEmpty($expected_result, $render_array, $stripTags = ''): void {
    $result = $this->extension->isNotEmpty($render_array, $stripTags);
    $this->assertSame($expected_result, $result);
  }

  /**
   * @dataProvider providerTestNotEmpty
   */
  public function testNotEmpty($expected_result, $render_array, $stripTags = ''): void {
    $result = $this->extension->isEmpty($render_array, $stripTags);
    $this->assertSame(!($expected_result), $result);
  }

  public function providerTestNotEmpty(): array {
    return [
      [true, 'This is a test a simple test'],
      [true, '   this is a test with text, padding

         and returns   '],
      [true, '   this is a test with text, left padding

         and returns', '', null],
      [true, "   this is a test with text, left padding

         and returns", "", null],
      [true, '<p><h1>This is a test with tags</h1>this is a test</p>'],
      [true, '<p>
                <h1>This is a test with tags</h1>
                this is a test
              </p>'],
      [true, '    <p><h1>This is a test with tags and padding</h1>this is a test</p>     '],
      [true, '    <p><h1>This is a test with tags and left padding</h1>this is a test</p>', '', null],
      [false, '<img src="http://www.example.com/image.png" alt="This test only contains tags" />'],
      [true, '<img src="http://www.example.com/image.png" alt="This test only contains tags">', '<img>'],
      [false, ''],
      [false, '\n\r       ', '', ''],
      [false, '     \n\r       ', '', ''],
      [false, "\n\r       ", "", ""],
      [false, "     \n\r       ", "", ""],
      [true, '\n\rthis is a test with characterMask and right padding       '],
      [true, [
        '#prefix' => '<p class="wrapper">',
        '#suffix' => '</p>',
        '#markup' => 'This is a markup renderable array',
        '#printed' => true
      ]]
    ];
  }

}