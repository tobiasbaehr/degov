<?php

namespace Drupal\Tests\degov_theming\Kernel;

use Drupal\degov_theming\Service\Template;
use Drupal\Tests\token\Kernel\KernelTestBase;

class TemplateTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = ['degov_theming'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }

  public function testRender() {
    /**
     * @var Template $template
     */
    $template = \Drupal::service('degov_theming.template');
    $html = $template->render('degov_theming', 'tests/src/Kernel/Fixture/template.html.twig', ['test' => 'some string']);

    $this->assertSame($html, '<span>some string</span>');
  }

}