<?php
declare(strict_types=1);

namespace Drupal\Tests\degov_common\Kernel;


use Drupal\Core\Config\ConfigImporterException;
use \Drupal\KernelTests\KernelTestBase;
use \Drupal\degov_common\DegovModuleUpdater;

/**
 * Class DegovModuleUpdaterTest
 *
 * @package Drupal\Tests\degov_common\Kernel
 */
class DegovModuleUpdaterTest extends KernelTestBase {

  const TEST_MODULE = 'degov_common_missing_dependency';

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'system',
    'field',
    'degov_common',
    'media',
    'video_embed_field',
    'block'
  ];

  /**
   * @var DegovModuleUpdater
   */
  private $moduleUpdater;

  /**
   * {@inheritdoc}
   * @throws \Exception
   */
  protected function setUp() : void {
    parent::setUp();
    $this->installConfig(['field', 'system']);
    $this->setInstallProfile('degov');
    /** @var \Drupal\Core\Extension\ThemeInstallerInterface $theme_installer */
    $theme_installer = $this->container->get('theme_installer');
    $theme_installer->install(['degov_theme']);
    \Drupal::configFactory()
      ->getEditable('system.theme')
      ->set('default', 'degov_theme')
      ->save();

    \Drupal::service('theme.manager')->resetActiveTheme();
    $this->moduleUpdater = $this->container->get('degov_config.module_updater');
  }

  /**
   * Test config import validation of degov_module/config/update_1234/install and
   * degov_module/config/update_5678/block
   *
   * @throws \Exception
   */
  public function testApplyUpdates() : void {
    $this->enableModules([self::TEST_MODULE]);
    $module_handler = $this->container->get('module_handler');
    self::assertTrue($module_handler->moduleExists(self::TEST_MODULE));
    /** @var \Drupal\Core\Config\ConfigFactoryInterface $config_factory */
    $config_factory = $this->container->get('config.factory');
    $config_name = 'field.storage.media.field_title';
    $config = $config_factory->get($config_name);
    self::assertTrue($config->isNew());
    self::assertEmpty($config->getRawData());

    // Drupal do not check the config dependencies here? All is fine via UI.
    // TODO: Find out why.
    $this->installConfig([self::TEST_MODULE]);

    $config = $config_factory->get($config_name);
    self::assertFalse($config->isNew());
    self::assertNotEmpty($config->getRawData());

    try {
      $this->moduleUpdater->applyUpdates(self::TEST_MODULE, '1234');
    }
    catch (ConfigImporterException $e) {
      $expected = <<<EOF
There were errors validating the config synchronization.
Configuration <em class="placeholder">$config_name</em> depends on the <em class="placeholder">non_existing</em> module that will not be installed after import.
EOF;
      $this->assertEqual($e->getMessage(), $expected);
    }
    catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }
    /** @var \Drupal\degov_common\DegovBlockInstallerInterface $block_installer */
    $block_installer = $this->container->get('degov_config.block_installer');
    $block_installer->placeBlockConfig(self::TEST_MODULE);
    try {
      $this->moduleUpdater->applyUpdates(self::TEST_MODULE, '5678');
    }
    catch (ConfigImporterException $e) {
      $expected = <<<EOF
There were errors validating the config synchronization.
Configuration <em class="placeholder">block.block.sidebarparagraphsfromnodeentity</em> depends on the <em class="placeholder">non_existing</em> module that will not be installed after import.
EOF;
      $this->assertEqual($e->getMessage(), $expected);
    }
    catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }
  }

}
