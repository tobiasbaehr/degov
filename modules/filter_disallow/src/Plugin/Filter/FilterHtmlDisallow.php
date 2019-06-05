<?php

namespace Drupal\filter_disallow\Plugin\Filter;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\Html;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

/**
 * Provides a filter to remove disallowed HTML tags.
 *
 * @Filter(
 *   id = "filter_html_disallow",
 *   title = @Translation("Remove disallowed HTML tags"),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_HTML_RESTRICTOR,
 *   settings = {
 *     "disallowed_html" = "<script>",
 *     "filter_html_disallow_help" = TRUE,
 *   },
 *   weight = -10
 * )
 */
class FilterHtmlDisallow extends FilterBase {

  /**
   * The processed HTML restrictions.
   *
   * @var array
   */
  protected $removals;

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['disallowed_html'] = [
      '#type'          => 'textarea',
      '#title'         => $this->t('Disallowed HTML tags'),
      '#default_value' => $this->settings['disallowed_html'],
      '#description'   => $this->t('A list of HTML tags that will be removed.'),
    ];
    $form['filter_html_disallow_help'] = [
      '#type'          => 'checkbox',
      '#title'         => $this->t('Display basic HTML help in long filter tips'),
      '#default_value' => $this->settings['filter_html_disallow_help'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    if (isset($configuration['settings']['disallowed_html'])) {
      // The javascript in core/modules/filter/filter.filter_html.admin.js
      // removes new lines and double spaces so, for consistency when javascript
      // is disabled, remove them.
      $configuration['settings']['disallowed_html'] = preg_replace('/\s+/', ' ', $configuration['settings']['disallowed_html']);
    }
    parent::setConfiguration($configuration);
    // Force restrictions to be calculated again.
    $this->removals = NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $removals = $this->getHTMLRemovals();
    foreach ($removals['disallowed'] as $removal) {
      $text = $this->stripHtmlTag($text, $removal);
    }
    return new FilterProcessResult($text);
  }

  /**
   * @param string $htmlContent
   * @param string $tag
   *
   * @return string
   */
  public function stripHtmlTag(string $htmlContent, string $tag): string {
    $dom = new \DOMDocument();
    // Prevent warnings on HTML5 elements.
    libxml_use_internal_errors(TRUE);
    $dom->loadHTML(utf8_decode($htmlContent));
    $xPath = new \DOMXPath($dom);
    $nodes = $xPath->query('//' . $tag);
    if ($nodes->length > 0) {
      if (\Drupal::currentUser()->hasPermission('view filter_disallow messages')) {
        \Drupal::messenger()->addWarning(t('The text you entered contains <code>:element</code> tags. These are not permitted here and will be stripped from the output.', [':element' => $tag]));
      }
      foreach ($nodes as $index => $node) {
        $node->parentNode->removeChild($nodes->item($index));
      }
    }
    $filteredHtml = $dom->saveHTML($dom->documentElement);
    // saveHTML() will urlencode characters like square brackets, which we need to remain intact for the media_file_links input filter.
    $filteredHtml = urldecode($filteredHtml);
    $filteredHtml = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $filteredHtml);
    return $filteredHtml;
  }

  /**
   * {@inheritdoc}
   */
  public function getHTMLRemovals() {
    if ($this->removals) {
      return $this->removals;
    }

    // Parse the allowed HTML setting, and gradually make the whitelist more
    // specific.
    $removals = ['disallowed' => []];

    // Make all the tags self-closing, so they will be parsed into direct
    // children of the body tag in the DomDocument.
    $html = str_replace('>', ' />', $this->settings['disallowed_html']);
    // Protect any trailing * characters in attribute names, since DomDocument
    // strips them as invalid.
    $star_protector = '__zqh6vxfbk3cg__';
    $html = str_replace('*', $star_protector, $html);
    $body_child_nodes = Html::load($html)
      ->getElementsByTagName('body')
      ->item(0)->childNodes;

    foreach ($body_child_nodes as $node) {
      if ($node->nodeType !== XML_ELEMENT_NODE) {
        // Skip the empty text nodes inside tags.
        continue;
      }
      $tag = $node->tagName;
      // Mark the tag as allowed, but with no attributes allowed.
      $removals['disallowed'][] = $tag;
    }

    // Save this calculated result for re-use.
    $this->removals = $removals;

    return $removals;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    $disallowed_html = $this->settings['disallowed_html'];

    if (empty($disallowed_html) || $this->settings['filter_html_disallow_help'] === FALSE) {
      return;
    }

    $output = $this->t('Disallowed HTML tags: <code>@tags</code>', ['@tags' => $disallowed_html]);
    if (!$long) {
      return $output;
    }

    return '<p>' . $output . '</p>';
  }

}
