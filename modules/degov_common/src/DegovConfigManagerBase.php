<?php

declare(strict_types=1);

namespace Drupal\degov_common;

use Drupal\Core\Config\ConfigImporter;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Config\StorageComparer;
use Drupal\Core\Config\StorageInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ModuleInstallerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Lock\LockBackendInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class DegovConfigManagerBase. Base class for deGov configuration updaters.
 *
 * @package Drupal\degov_common
 */
class DegovConfigManagerBase implements DegovConfigManagerInterface {
  use StringTranslationTrait;
  /**
   * Active storage.
   *
   * @var \Drupal\Core\Config\StorageInterface
   */
  protected $activeStorage;

  /**
   * The event dispatcher used to notify subscribers.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * The configuration manager.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;

  /**
   * The used lock backend instance.
   *
   * @var \Drupal\Core\Lock\LockBackendInterface
   */
  protected $lock;

  /**
   * The typed config manager.
   *
   * @var \Drupal\Core\Config\TypedConfigManagerInterface
   */
  protected $typedConfigManager;

  /**
   * The module handler.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The theme handler.
   *
   * @var \Drupal\Core\Extension\ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * The module installer.
   *
   * @var \Drupal\Core\Extension\ModuleInstallerInterface
   */
  protected $moduleInstaller;

  /**
   * Translation Interface service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected $stringTranslation;

  /**
   * Constructs a configuration import object.
   *
   * @param \Drupal\Core\Config\StorageInterface $active_storage
   *   Active storage.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher used to notify subscribers of config import events.
   * @param \Drupal\Core\Config\ConfigManagerInterface $config_manager
   *   The configuration manager.
   * @param \Drupal\Core\Lock\LockBackendInterface $lock
   *   The lock backend to ensure multiple imports do not occur at the same
   *   time.
   * @param \Drupal\Core\Config\TypedConfigManagerInterface $typed_config
   *   The typed configuration manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Extension\ModuleInstallerInterface $module_installer
   *   The module installer.
   * @param \Drupal\Core\Extension\ThemeHandlerInterface $theme_handler
   *   The theme handler.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $string_translation
   *   The string translation service.
   */
  public function __construct(StorageInterface $active_storage, EventDispatcherInterface $event_dispatcher, ConfigManagerInterface $config_manager, LockBackendInterface $lock, TypedConfigManagerInterface $typed_config, ModuleHandlerInterface $module_handler, ModuleInstallerInterface $module_installer, ThemeHandlerInterface $theme_handler, TranslationInterface $string_translation) {
    $this->activeStorage = $active_storage;
    $this->eventDispatcher = $event_dispatcher;
    $this->configManager = $config_manager;
    $this->lock = $lock;
    $this->typedConfigManager = $typed_config;
    $this->moduleHandler = $module_handler;
    $this->moduleInstaller = $module_installer;
    $this->themeHandler = $theme_handler;
    $this->stringTranslation = $string_translation;
  }

  /**
   * Imports all the changes for the configuration with batch.
   *
   * @param \Drupal\Core\Config\StorageInterface $sourceStorage
   *   Source storage.
   */
  public function configImport(StorageInterface $sourceStorage) : void {
    $storage_comparer = new StorageComparer($sourceStorage, $this->activeStorage);

    if (!$storage_comparer->createChangelist()->hasChanges()) {
      \Drupal::messenger()->addWarning($this->t('There are no changes to import.'));
      return;
    }
    $config_importer = new ConfigImporter(
      $storage_comparer,
      $this->eventDispatcher,
      $this->configManager,
      $this->lock,
      $this->typedConfigManager,
      $this->moduleHandler,
      $this->moduleInstaller,
      $this->themeHandler,
      $this->stringTranslation
    );
    // Inspired by
    // vendor/drush/drush/src/Drupal/Commands/config/ConfigImportCommands.php.
    if ($config_importer->alreadyImporting()) {
      \Drupal::messenger()->addError($this->t('Another request may be importing configuration already.'));
    }
    else {
      if ($config_importer->hasUnprocessedConfigurationChanges()) {
        $sync_steps = $config_importer->initialize();
        foreach ($sync_steps as $step) {
          $context = [];
          do {
            $config_importer->doSyncStep($step, $context);
            if (isset($context['message'])) {
              // Message is already translated.
              \Drupal::messenger()->addStatus(str_replace('Synchronizing', 'Synchronized', (string) $context['message']));
            }
          } while ($context['finished'] < 1);
        }
        if (!$config_importer->getErrors()) {
          \Drupal::messenger()->addStatus($this->t('The configuration was imported successfully.'));
        }
      }

    }
  }

  /**
   * Adds the uuid to the data array in case there no uuid set.
   *
   * @param string $configName
   *   Name of the config.
   * @param array $data
   *   Config data.
   */
  protected function addUuid($configName, array &$data) : void {
    if (!isset($data['uuid'])) {
      $config = $this->configManager->getConfigFactory()->get($configName);
      if (!$config->isNew()) {
        /** @var \Drupal\Component\Uuid\Php $uuid_service */
        $uuid_service = \Drupal::service('uuid');
        $data['uuid'] = $config->get('uuid') ?? $uuid_service->generate();
      }
    }
  }

}
