<?php

declare(strict_types=1);

namespace Drupal\degov_hyphenopoly;

use Drupal\Component\Render\MarkupInterface;
use Drupal\Core\Render\Markup;
use Drupal\Core\Security\TrustedCallbackInterface;

/**
 * Class StripDebug
 *
 * @package Drupal\degov_hyphenopoly
 */
class StripDebug implements TrustedCallbackInterface {

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['postRenderStripDebug'];
  }

  /**
   * Remove twig debug output.
   */
  public static function postRenderStripDebug(MarkupInterface $build): string {
    return (string) Markup::create(trim(strip_tags((string) $build)));
  }

}
