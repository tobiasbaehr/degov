<?php

namespace Drupal\Tests\degov_auto_crop\Kernel;

use Drupal\Tests\token\Kernel\KernelTestBase;

class OffsetConfigTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'lightning_core',
    'image',
    'responsive_image',
    'breakpoint',
    'crop',
    'media',
    'degov_image_and_crop_styles',
    'degov_auto_crop',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->installEntitySchema('crop_type', 'media');
    $this->installConfig([
      'image',
      'responsive_image',
      'crop',
      'degov_image_and_crop_styles',
      'degov_auto_crop',
    ]);
  }

  public function testOffsetValuesAreInstalled() {
    $expectedValues = [
      'degov_auto_crop' => [
        'offsets' => [
          'landscape' => [
            'top'    => 1,
            'bottom' => 2,
            'left'   => 1,
            'right'  => 1,
          ],
          'portrait'  => [
            'top'    => 1,
            'bottom' => 2,
            'left'   => 1,
            'right'  => 1,
          ],
          'square'    => [
            'top'    => 1,
            'bottom' => 2,
            'left'   => 1,
            'right'  => 1,
          ],
        ],
      ],
    ];
    $settings = \Drupal::config('crop.type.16_to_9')
      ->get('third_party_settings');
    self::assertEquals($expectedValues, $settings);
  }

}