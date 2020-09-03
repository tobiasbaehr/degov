<?php

declare(strict_types=1);

namespace Drupal\media_file_links\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\filter\FilterProcessResult;
use Drupal\linkit\Plugin\Filter\LinkitFilter;

/**
 * Provides a Linkit filter.
 *
 * @Filter(
 *   id = "linkit",
 *   title = @Translation("Linkit URL converter"),
 *   description = @Translation("Updates links inserted by Linkit to point to entity URL aliases."),
 *   settings = {
 *     "title" = TRUE,
 *   },
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE
 * )
 */
final class LinkitFilterExtended extends LinkitFilter {

  /**
   * {@inheritDoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    if (strpos($text, 'data-entity-type') !== FALSE) {
      $dom = Html::load($text);
      $xpath = new \DOMXPath($dom);

      foreach ($xpath->query('//a[@data-entity-type]') as $element) {
        /** @var \DOMElement $element */
        if ($element->getAttribute('data-entity-type') === 'media' && preg_match('/[\[<]media\/file\/([\d]+)[\]>]/', $element->getAttribute('href'))) {
          $element->removeAttribute('data-entity-type');
        }
      }
      $text = Html::serialize($dom);
    }

    return parent::process($text, $langcode);
  }

}
