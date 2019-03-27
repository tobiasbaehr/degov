<?php

namespace Drupal\Tests\degov_demo_content\Unit;

use Drupal\Core\Entity\EntityFieldManager;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\degov_demo_content\MediaBundle;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;


class MediaBundleTest extends UnitTestCase {

  public function testBundleHasField(): void {
    $entityFieldManager = $this->prophesize(EntityFieldManagerInterface::class);
    $entityBundleInfo = $this->prophesize(EntityTypeBundleInfoInterface::class);
    $entityFieldManager->getFieldDefinitions(Argument::type('string'), Argument::type('string'))->willReturn(
      [
        'field_1' => 'value1',
        'field_2' => 'value2',
      ]
    );
    $mediaBundle = new MediaBundle($entityFieldManager->reveal(), $entityBundleInfo->reveal());
    self::assertTrue($mediaBundle->bundleHasField('field_1', 'facts'));
    self::assertFalse($mediaBundle->bundleHasField('field_3', 'facts'));
  }

  public function testBundleExistsInStorage(): void {
    $entityFieldManager = $this->prophesize(EntityFieldManagerInterface::class);
    $entityBundleInfo = $this->prophesize(EntityTypeBundleInfoInterface::class);
    $entityBundleInfo->getBundleInfo(Argument::type('string'))->willReturn([
      'bundle_1' => [],
      'bundle_2' => [],
      'bundle_3' => []
    ]);
    $mediaBundle = new MediaBundle($entityFieldManager->reveal(), $entityBundleInfo->reveal());
    self::assertTrue($mediaBundle->bundleExistsInStorage('bundle_1'));
    self::assertFalse($mediaBundle->bundleExistsInStorage('bundle_77'));
  }

}
