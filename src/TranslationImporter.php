<?php

declare(strict_types=1);

namespace Drupal\degov;

use Drupal\language\Config\LanguageConfigOverride;
use Drupal\locale\Gettext;
use Symfony\Component\Yaml\Yaml;

/**
 * Class TranslationImporter.
 *
 * @package Drupal\degov
 */
class TranslationImporter {

  /**
   * Import for profile.
   *
   * @param string $installationProfile
   *   The installation profile to import translations for.
   * @param string $langcode
   *   The language to import for.
   *
   * @throws \Exception
   */
  public static function importForProfile(string $installationProfile = 'degov', string $langcode = 'de-de'): void {
    self::import($installationProfile, $langcode);
  }

  /**
   * Import for profiles.
   *
   * @param array $installationProfiles
   *   The installation profiles to import translations for.
   * @param string $langcode
   *   The language to import for.
   *
   * @throws \Exception
   */
  public static function importForProfiles(array $installationProfiles, string $langcode = 'de-de'): void {
    foreach ($installationProfiles as $installationProfile) {
      self::importForProfile($installationProfile, $langcode);
    }
  }

  /**
   * Import.
   *
   * @param string $installationProfile
   *   The installation profile to import translations for.
   * @param string $langcode
   *   The language to import for.
   *
   * @throws \Exception
   */
  private static function import(string $installationProfile, string $langcode): void {
    self::importFromPoFile(drupal_get_path('profile', $installationProfile) . '/translations/' . $langcode . '.po', $langcode);
  }

  /**
   * Import translation from a singe PO file.
   *
   * @param string $filepath
   *   The file to import.
   * @param string $langcode
   *   The language to import for.
   *
   * @throws \Exception
   */
  public static function importFromPoFile(string $filepath, string $langcode): void {
    self::importFromPoFiles([$filepath], $langcode);
  }

  /**
   * Import translations from a collection of PO files.
   *
   * @param array $filepaths
   *   The PO files tom import.
   * @param string $langcode
   *   The language to import for.
   * @param bool $preventOverwrite
   *   Should the translations be protected from being overwritten?
   *
   * @throws \Exception
   */
  public static function importFromPoFiles(array $filepaths, string $langcode, bool $preventOverwrite = TRUE): void {
    foreach ($filepaths as $filepath) {
      if (!file_exists($filepath)) {
        continue;
      }

      /** @var \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler */
      $moduleHandler = \Drupal::moduleHandler();
      $moduleHandler->loadInclude('locale', 'bulk.inc');
      $moduleHandler->loadInclude('locale', 'translation.inc');
      $file = locale_translate_file_create($filepath);
      $file->langcode = $langcode;
      $options = array_merge(_locale_translation_default_update_options(), [
        'overwrite_options' => [
          'not_customized' => TRUE,
          'customized'     => TRUE,
        ],
        'customized'        => $preventOverwrite ? LOCALE_CUSTOMIZED : LOCALE_NOT_CUSTOMIZED,
      ]);
      $file = locale_translate_file_attach_properties($file, $options);
      $file->langcode = $langcode;
      $batch = locale_translate_batch_build([$file->uri => $file], $options);
      batch_set($batch);
    }
  }

  /**
   * Imports translations from PO files in a given directory.
   *
   * @param string $directoryPath
   *   The directory containing the PO files.
   * @param string $langcode
   *   The language to import for.
   * @param bool $preventOverwrite
   *   Should the translations be protected from being overwritten?
   *
   * @throws \Exception
   */
  public static function importTranslationsFromDirectory(string $directoryPath, string $langcode, bool $preventOverwrite = TRUE): void {
    $filesToImport = self::findPoFilesInDirectory($directoryPath);
    self::importFromPoFiles($filesToImport, $langcode, $preventOverwrite);
  }

  /**
   * Locates all PO files in a given directory.
   *
   * @param string $directoryPath
   *   The directory to search in.
   *
   * @return array
   *   The PO files found in this directory.
   */
  private static function findPoFilesInDirectory(string $directoryPath): array {
    $directoryHandle = opendir($directoryPath);
    $filesToImport = [];

    while ($fileName = readdir($directoryHandle)) {
      if (!\in_array($fileName, ['.', '..'])) {
        $filePath = $directoryPath . '/' . $fileName;
        if (is_file($filePath) && preg_match("/^([a-zA-Z0-9\_]+)\.[a-z]+\.po$/", $fileName, $fileNameParts)) {
          $filesToImport[] = $filePath;
        }
        else {
          if (is_dir($filePath)) {
            $filesToImport += self::findPoFilesInDirectory($filePath);
          }
        }
      }
    }

    return $filesToImport;
  }

  /**
   * Imports config translations from a given directory.
   *
   * @param string $langcode
   *   The target language code.
   * @param string $pathToDirectory
   *   The directory containing the config translations.
   */
  public static function importConfigTranslationsFromDirectory(string $langcode, string $pathToDirectory): void {
    $directoryHandle = opendir($pathToDirectory);
    if ($directoryHandle !== FALSE) {
      while (($fileName = readdir($directoryHandle)) !== FALSE) {
        if (preg_match("/([a-zA-Z0-9\.-_]+)\.yml$/", $fileName, $matches)) {
          if (count($matches) === 2) {
            self::importConfigTranslation($langcode, $matches[1], $pathToDirectory . '/' . $matches[0]);
          }
        }
      }
      closedir($directoryHandle);
    }
  }

  /**
   * Imports translations for a given configuration.
   *
   * @param string $configName
   *   The name of the config we are translating.
   * @param string $module
   *   Name of the module.
   * @param string $langcode
   *   The target language code.
   */
  public static function importConfigTranslationOfModule(string $configName, string $module, string $langcode = 'de'): void {
    $pathToFile = \Drupal::service('extension.list.module')
      ->get($module)
      ->getPath() . "/config/install/language/$langcode/$configName.yml";
    self::importConfigTranslation($langcode, $configName, $pathToFile);
  }

  /**
   * Imports translations for a given configuration.
   *
   * @param string $langcode
   *   The target language code.
   * @param string $configName
   *   The name of the config we are translating.
   * @param string $pathToFile
   *   The path to the translation file.
   */
  public static function importConfigTranslation(string $langcode, string $configName, string $pathToFile): void {
    $configOverride = \Drupal::service('language.config_factory_override')->getOverride($langcode, $configName);
    $fileContents = Yaml::parseFile($pathToFile);
    self::processConfigTranslationFileContents($fileContents, $configOverride);
    $configOverride->save();
  }

  /**
   * Processes an array of config data.
   *
   * @param array $configArray
   *   The config array to process.
   * @param \Drupal\language\Config\LanguageConfigOverride $languageConfigOverride
   *   The LanguageConfigOverride storing our translations.
   * @param array $currentConfigKeyPath
   *   An array containing the config path, for recursion.
   */
  private static function processConfigTranslationFileContents(array $configArray, LanguageConfigOverride $languageConfigOverride, array $currentConfigKeyPath = []) {
    foreach ($configArray as $key => $value) {
      $nextConfigKeyPath = array_merge($currentConfigKeyPath, [$key]);
      if (\is_array($value)) {
        self::processConfigTranslationFileContents($value, $languageConfigOverride, $nextConfigKeyPath);
      }
      else {
        $languageConfigOverride->set(implode('.', $nextConfigKeyPath), $value);
      }
    }
  }

}
