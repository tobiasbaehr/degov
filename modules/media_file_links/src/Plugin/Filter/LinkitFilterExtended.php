<?php declare(strict_types=1);

namespace Drupal\media_file_links\Plugin\Filter;


use Drupal\Component\Utility\Html;
use Drupal\filter\FilterProcessResult;
use Drupal\linkit\Plugin\Filter\LinkitFilter;
use Drupal\media_file_links\Service\MediaFileLinkPlaceholderHandler;
use Drupal\media_file_links\Service\MediaFileLinkResolver;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
   * @var \Drupal\media_file_links\Service\MediaFileLinkResolver
   */
  protected $fileLinkResolver;

  /**
   * @var \Drupal\media_file_links\Service\MediaFileLinkPlaceholderHandler
   */
  protected $placeholderHandler;

  /**
   * @param \Drupal\media_file_links\Service\MediaFileLinkResolver $file_link_resolver
   */
  public function setFileLinkResolver(MediaFileLinkResolver $file_link_resolver): void {
    $this->fileLinkResolver = $file_link_resolver;
  }

  /**
   * @param \Drupal\media_file_links\Service\MediaFileLinkPlaceholderHandler $placeholder_handler
   */
  public function setPlaceholderHandler(MediaFileLinkPlaceholderHandler $placeholder_handler): void {
    $this->placeholderHandler = $placeholder_handler;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->setFileLinkResolver($container->get('media_file_links.file_link_resolver'));
    $instance->setPlaceholderHandler($container->get('media_file_links.placeholder_handler'));
    return $instance;
  }


  /**
   * {@inheritDoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    $has_media_media_links = FALSE;
    while (($mediaId = $this->placeholderHandler->getMediaIdFromPlaceholder($text)) !== NULL) {
      $fileUrl = $this->fileLinkResolver->getFileUrlString($mediaId);
      $text = str_replace($this->placeholderHandler->getPlaceholderForMediaId($mediaId), $fileUrl, $text);
      $has_media_media_links = TRUE;
    }

    if ($has_media_media_links && strpos($text, 'data-entity-type') !== FALSE) {
      $dom = Html::load($text);
      $xpath = new \DOMXPath($dom);

      foreach ($xpath->query('//a[@data-entity-type]') as $element) {
        /** @var \DOMElement $element */
        try {
          if ($element->getAttribute('data-entity-type') === 'media') {
            $element->removeAttribute('data-entity-type');
          }

        }
        catch (\Exception $e) {
          watchdog_exception('media_file_links', $e);
        }
      }

      $text = Html::serialize($dom);
    }

    return parent::process($text, $langcode);
  }


}
