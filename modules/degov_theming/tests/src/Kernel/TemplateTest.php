<?php

namespace Drupal\Tests\degov_common\Kernel;

use Drupal\Core\Asset\LibraryDiscovery;
use Drupal\Core\Entity\Entity;
use Drupal\Core\Theme\ActiveTheme;
use Drupal\Core\Theme\ThemeManager;
use Drupal\degov_theming\Factory\FilesystemFactory;
use Drupal\degov_theming\Service\DrupalPath;
use Drupal\degov_theming\Service\Template;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;
use Symfony\Component\Filesystem\Filesystem;


class TemplateTest extends UnitTestCase {

  /**
   * @var Template $template
   */
  private $template;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'degov_theming',
    'degov_node_press'
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->mockThemeManager();
  }

  /**
   * @return DrupalPath
   */
  private function mockDrupalPath() {
    $drupalPath = $this->prophesize(DrupalPath::class);
    $drupalPath->getPath(Argument::type('string'), Argument::type('string'))
      ->willReturn('profiles/contrib/degov/degov_node_normal_page');

    return $drupalPath->reveal();
  }

  /**
   * @return ThemeManager
   */
  private function mockThemeManager() {
    $themeManager = $this->prophesize(ThemeManager::class);

    $activeThemeStub = $this->prophesize(ActiveTheme::class);

    $baseTheme = $this->prophesize(ActiveTheme::class);
    $baseTheme->getPath()->willReturn('themes/custom/base_theme/');

    $projectTheme = $this->prophesize(ActiveTheme::class);
    $projectTheme->getBaseThemes()->willReturn([
      $baseTheme->reveal(),
      $activeThemeStub->reveal(),
    ]);
    $projectTheme->getPath()->willReturn('themes/custom/project_theme/');
    $projectTheme->reveal();

    $themeManager->getActiveTheme()->willReturn($projectTheme);

    return $themeManager->reveal();
  }

  /**
   * @return LibraryDiscovery
   */
  private function mockLibraryDiscovery() {
    $libraryDiscovery = $this->prophesize(LibraryDiscovery::class);
    $libraryDiscovery->getLibraryByName(Argument::type('string'), Argument::type('string'))
      ->willReturn('any.library');

    return $libraryDiscovery->reveal();
  }

  /**
   * @return FilesystemFactory
   */
  private function mockFilesystemFactory($findsModulesTemplate = TRUE) {
    $filesystem = $this->prophesize(Filesystem::class);
    $filesystem->exists(Argument::type('string'))->shouldBeCalled()->willReturn($findsModulesTemplate);

    $filesystemFactory = $this->prophesize(FilesystemFactory::class);
    $filesystemFactory->create()->willReturn($filesystem->reveal());

    return $filesystemFactory->reveal();
  }

  public function getPreprocess() {
    $out = [];

    $out[] = [
      'hook'    => 'node',
      'info'    => [
        'render element' => 'elements',
        'type'           => 'base_theme',
        'theme path'     => 'themes',
        'template'       => 'node--normal_page--default',
        'path'           => 'themes/base-theme/templates',
      ],
      'options' => [
        'module_name'       => 'degov_node_normal_page',
        'entity_type'       => 'node',
        'entity_bundles'    =>
          [
            0 => 'normal_page',
          ],
        'entity_view_modes' =>
          [
            0 => 'full',
            1 => 'long_text',
            2 => 'preview',
            3 => 'slim',
            4 => 'small_image',
          ],
      ],
    ];

    return $out;
  }

  public function getClientThemePreprocess() {
    $out = [];

    $out[] = [
      'hook'    => 'node',
      'info'    => [
        'render element' => 'elements',
        'type'           => 'base_theme',
        'theme path'     => 'themes',
        'template'       => 'node--normal_page--default',
        'path'           => 'themes/client-theme/templates',
      ],
      'options' => [
        'module_name'       => 'degov_node_normal_page',
        'entity_type'       => 'node',
        'entity_bundles'    =>
          [
            0 => 'normal_page',
          ],
        'entity_view_modes' =>
          [
            0 => 'full',
            1 => 'long_text',
            2 => 'preview',
            3 => 'slim',
            4 => 'small_image',
          ],
      ],
    ];

    return $out;
  }

  /**
   * @dataProvider getPreprocess()
   */
  public function testSuggestModuleTemplate($hook, $info, $options) {
    $this->template = new Template($this->mockThemeManager(), $this->mockLibraryDiscovery(), $this->mockFilesystemFactory(), $this->mockDrupalPath());

    $node = $this->prophesize(Entity::class);
    $node->bundle()->willReturn('normal_page');

    $variables = [
      'elements' => [
        '#view_mode' => 'preview'
      ],
      'node' => $node->reveal()
    ];

    $this->template->suggest($variables, $hook, $info, $options);

    $this->assertArrayEquals(
      [
        'render element' => 'elements',
        'type'           => 'base_theme',
        'theme path'     => 'modules',
        'template'       => 'node--normal_page--preview',
        'path'           => 'profiles/contrib/degov/degov_node_normal_page/templates',
      ],
      $info
    );
  }

  /**
   * @dataProvider getPreprocess()
   */
  public function testSuggestInheritedThemeTemplate($hook, $info, $options) {
    $this->template = new Template($this->mockThemeManager(), $this->mockLibraryDiscovery(), $this->mockFilesystemFactory(FALSE), $this->mockDrupalPath());

    $node = $this->prophesize(Entity::class);
    $node->bundle()->willReturn('normal_page');

    $variables = [
      'elements' => [
        '#view_mode' => 'preview'
      ],
      'node' => $node->reveal()
    ];

    $this->template->suggest($variables, $hook, $info, $options);

    $this->assertArrayEquals(
      [
        'render element' => 'elements',
        'type'           => 'base_theme',
        'theme path'     => 'themes',
        'template'       => 'node--normal_page--default',
        'path'           => 'themes/base-theme/templates',
      ],
      $info
    );
  }

  /**
   * @dataProvider getClientThemePreprocess()
   */
  public function testSuggestProjectThemeTemplate($hook, $info, $options) {
    $this->template = new Template($this->mockThemeManager(), $this->mockLibraryDiscovery(), $this->mockFilesystemFactory(FALSE), $this->mockDrupalPath());

    $node = $this->prophesize(Entity::class);
    $node->bundle()->willReturn('normal_page');

    $variables = [
      'elements' => [
        '#view_mode' => 'preview'
      ],
      'node' => $node->reveal()
    ];

    $this->template->suggest($variables, $hook, $info, $options);

    $this->assertArrayEquals(
      [
        'render element' => 'elements',
        'type'           => 'base_theme',
        'theme path'     => 'themes',
        'template'       => 'node--normal_page--default',
        'path'           => 'themes/client-theme/templates',
      ],
      $info
    );
  }

}