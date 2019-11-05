<?php

namespace Drupal\degov;

use Drupal\locale\Gettext;

class TranslationImporter {

  public static function importForProfile(string $installationProfile = 'degov', string $langcode = 'de-de'): void {
    self::import($installationProfile, $langcode);
  }

  public static function importForProfiles(array $installationProfiles, string $langcode = 'de-de'): void {
    foreach ($installationProfiles as $installationProfile) {
      self::importForProfile($installationProfile, $langcode);
    }
  }

  private static function import(string $installationProfile, string $langcode): void {
    $file = new \stdClass();
    $file->uri = drupal_get_path('profile', $installationProfile) . '/translations/' . $langcode . '.po';
    $file->langcode = 'de';

    Gettext::fileToDatabase($file, [
      'overwrite_options' => [
        'not_customized' => TRUE,
        'customized'     => TRUE,
      ],
      'customized'        => LOCALE_CUSTOMIZED,
    ]);
  }

}
