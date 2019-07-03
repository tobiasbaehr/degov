<?php

namespace Drupal\Tests\degov_paragraph_downloads\Kernel;

use Drupal\easy_breadcrumb\EasyBreadcrumbBuilder;
use Drupal\language\Entity\ConfigurableLanguage;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\Core\Routing\RequestContext;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Core\Routing\RouteMatch;
use Drupal\Core\Url;
use Drupal\paragraphs\ParagraphsBehaviorManager;

class ModuleInstallationTest extends KernelTestBase {

  public static $modules = [
    'paragraphs',
    'degov_paragraph_downloads',
    'degov_content_types_shared_fields',
    'degov_common',
    'degov_media_document',
    'entity_browser',
    'field',
    'link',
    'media',
    'file',
    'video_embed_field',
    'locale',
    'language',
    'degov_paragraph_downloads_test',
  ];

  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('paragraph');
  }

  public function testSetup(): void {
    /**
     * @var ModuleHandler $moduleHandler
     */
    $moduleHandler = \Drupal::service('module_handler');
    self::assertTrue($moduleHandler->moduleExists('degov_paragraph_downloads'));
    self::assertTrue($moduleHandler->getModule('degov_paragraph_downloads'));
  }

  public function testConfigTranslation() {
    $this->installSchema('locale', ['locales_location', 'locales_source', 'locales_target']);
    $this->installConfig(['degov_paragraph_downloads_test']);
    $localeConfigManager = \Drupal::service('locale.config_manager');

    $language = ConfigurableLanguage::createFromLangcode('de');
    $language->save();

    // Check translated config files have translations available.
    $result = $localeConfigManager->hasTranslation('degov_paragraph_downloads_test.translation', $language->getId());
    $this->assertTrue($result, 'There is a translation for  degov_paragraph_downloads_test.translation configuration.');

    // Check No translated config files have no translations available.
    $result = $localeConfigManager->hasTranslation('degov_paragraph_downloads_test.no_translation', $language->getId());
    $this->assertFalse($result, 'There is no translation for  degov_paragraph_downloads_test.no_translation_id configuration.');
  }
}
