<?php

namespace Drupal\degov_media_image\Service;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystem;

/**
 * Class AutoCropper.
 *
 * Provides functions to automatically apply image crops to given files.
 *
 * @package Drupal\degov_media_image\Service
 */
class AutoCropper {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystem
   */
  protected $fileSystem;

  /**
   * Constructs a new AutoCropper.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\File\FileSystem $file_system
   *   The file system.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, FileSystem $file_system) {
    $this->entityTypeManager = $entity_type_manager;
    $this->fileSystem = $file_system;
  }

  /**
   * Apply all defined crop types to all image files in our definitions.
   *
   * @param \Drupal\file\Entity\File $file
   *   The File entity the image crops should be applied to.
   */
  public function applyImageCrops($file): void {
    try {
      $crop_types = $this->entityTypeManager
        ->getStorage('crop_type')
        ->loadMultiple();
      if (preg_match("/^image\//", $file->getMimeType())) {
        foreach ($crop_types as $crop_type) {
          try {
            $image_dimensions = getimagesize($this->fileSystem
              ->realpath($file->getFileUri()));

            $crop = $this->entityTypeManager
              ->getStorage('crop')
              ->loadByProperties([
                'type' => $crop_type->id(),
                'uri'  => $file->getFileUri(),
              ]);

            $measurements = $this->calculateCropDimensions($crop_type, $image_dimensions);

            if (empty($crop)) {
              $crop_values = [
                'type'        => $crop_type->id(),
                'entity_id'   => $file->id(),
                'entity_type' => 'file',
                'uri'         => $file->getFileUri(),
                'x'           => $image_dimensions[0] / 2,
                'y'           => $image_dimensions[1] / 2,
              ];
              $crop_values += $measurements;
              $crop = $this->entityTypeManager
                ->getStorage('crop')
                ->create($crop_values);
            }
            else {
              $crop = reset($crop);
              $crop->set('x', $image_dimensions[0] / 2);
              $crop->set('y', $image_dimensions[1] / 2);
              $crop->set('width', $measurements['width']);
              $crop->set('height', $measurements['height']);
            }
            $crop->save();
          } catch (PluginNotFoundException | InvalidPluginDefinitionException | EntityStorageException $exception) {
            // Crop not found or crop save failed. Log and continue.
            error_log($exception->getMessage());
          }
        }
      }
    } catch (PluginNotFoundException | InvalidPluginDefinitionException $exception) {
      // No crop types found. Just log, otherwise do nothing.
      error_log($exception->getMessage());
    }
  }

  /**
   * Calculate the height and width of the crop frame to be applied.
   *
   * @param object $crop_type
   *   The Crop crop_type we want to apply.
   * @param array $image_dimensions
   *   The actual width and height of the image file.
   *
   * @return array
   *   The desired height and width of the image.
   */
  private function calculateCropDimensions($crop_type, array $image_dimensions): array {
    $aspect_ratio_fragments = explode(':', $crop_type->aspect_ratio);
    if (count($aspect_ratio_fragments) !== 2) {
      $aspect_ratio_fragments = [1, 1];
    }

    if ($image_dimensions[0] >= $image_dimensions[1]) {
      // Landscape orientation.
      $measurements = [
        'width'  => (($image_dimensions[1] / $aspect_ratio_fragments[1]) * $aspect_ratio_fragments[0]) * $this->calculateScaleFactor($image_dimensions, $aspect_ratio_fragments),
        'height' => $image_dimensions[1] * $this->calculateScaleFactor($image_dimensions, $aspect_ratio_fragments),
      ];
    }
    else {
      // Portrait orientation.
      $measurements = [
        'width'  => $image_dimensions[0] * $this->calculateScaleFactor($image_dimensions, $aspect_ratio_fragments),
        'height' => (($image_dimensions[0] / $aspect_ratio_fragments[0]) * $aspect_ratio_fragments[1]) * $this->calculateScaleFactor($image_dimensions, $aspect_ratio_fragments),
      ];
    }

    return $measurements;
  }

  /**
   * Calculates a scape factor for the crop frame.
   *
   * @param array $image_dimensions
   *   The dimensions of the image.
   * @param array $aspect_ratio_fragments
   *   The aspect ratio of the crop type.
   *
   * @return float|int
   *   The factor to scale the image by.
   */
  private function calculateScaleFactor(array $image_dimensions, array $aspect_ratio_fragments) {
    if ($image_dimensions[0] > $image_dimensions[1]) {
      $image_ratio = $image_dimensions[0] / $image_dimensions[1];
      $crop_ratio = $aspect_ratio_fragments[0] / $aspect_ratio_fragments[1];
    }
    else {
      $image_ratio = $image_dimensions[1] / $image_dimensions[0];
      $crop_ratio = $aspect_ratio_fragments[1] / $aspect_ratio_fragments[0];
    }
    if ($crop_ratio > $image_ratio) {
      return $image_ratio / $crop_ratio;
    }
    return 1;
  }

}
