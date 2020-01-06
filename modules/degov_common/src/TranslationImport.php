<?php

namespace Drupal\degov_common;

/**
 * Class TranslationImport.
 *
 * @package Drupal\degov_common
 */
class TranslationImport {

  /**
   * Import.
   *
   * Imports the german translations from the directory translations of the
   * component.
   *
   * @param string $name
   *   The name of the item for which the path is requested. Ignored for
   *   $type 'core'.
   * @param string $type
   *   The type of the item; one of 'core', 'profile', 'module', 'theme', or
   *   'theme_engine'.
   */
  public function import(string $name, string $type = 'module') : void {
    $filepath = drupal_get_path($type, $name) . '/translations/de-de.po';
    $langcode = 'de';
    if (!file_exists($filepath)) {
      return;
    }
    \Drupal::moduleHandler()->loadInclude('locale', 'bulk.inc');
    \Drupal::moduleHandler()->loadInclude('locale', 'translation.inc');
    $file = locale_translate_file_create($filepath);
    $file->langcode = $langcode;
    $options = array_merge(_locale_translation_default_update_options(), [
      'overwrite_options' => [
        'not_customized' => TRUE,
        'customized' => TRUE,
      ],
      'customized' => LOCALE_CUSTOMIZED,
    ]);
    $file = locale_translate_file_attach_properties($file, $options);
    $file->langcode = $langcode;
    $batch = locale_translate_batch_build([$file->uri => $file], $options);
    batch_set($batch);
  }

}
