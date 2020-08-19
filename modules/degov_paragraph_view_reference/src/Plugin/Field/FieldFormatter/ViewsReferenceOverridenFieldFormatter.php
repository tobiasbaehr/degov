<?php

declare(strict_types=1);

namespace Drupal\degov_paragraph_view_reference\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Utility\Token;
use Drupal\node\NodeInterface;
use Drupal\views\Entity\View;
use Drupal\viewsreference\Plugin\Field\FieldFormatter\ViewsReferenceFieldFormatter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Field formatter for Viewsreference Field.
 *
 * @FieldFormatter(
 *   id = "degov_viewsreference_formatter",
 *   label = @Translation("Views Reference with extra options"),
 *   field_types = {"viewsreference"}
 * )
 */
final class ViewsReferenceOverridenFieldFormatter extends ViewsReferenceFieldFormatter {

  /**
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $currentRouteMatch;

  /**
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * @param \Drupal\Core\Routing\RouteMatchInterface $currentRouteMatch
   */
  public function setCurrentRouteMatch(RouteMatchInterface $current_route_match): void {
    $this->currentRouteMatch = $current_route_match;
  }

  /**
   * @param \Drupal\Core\Utility\Token $token
   */
  public function setToken(Token $token): void {
    $this->token = $token;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->setCurrentRouteMatch($container->get('current_route_match'));
    $instance->setToken($container->get('token'));
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    foreach ($elements as $delta => $element) {
      unset($elements[$delta]['title']);
    }
    return $elements;
  }

}
