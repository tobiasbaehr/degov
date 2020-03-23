<?php

namespace Drupal\degov_common\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SidebarParagraphs.
 *
 * @Block(
 *   id = "sidebar_paragraphs",
 *   admin_label = @Translation("Sidebar paragraphs from Node entity"),
 *   category = @Translation("Blocks")
 * )
 */
class SidebarParagraphs extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Route matcher.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatcher;

  /**
   * SidebarParagraphs constructor.
   * phpcs:disable
   *
   * @param array $configuration
   *   Configration.
   * @param string $plugin_id
   *   Plugin ID.
   * @param mixed $plugin_definition
   *   Plugin definition.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   Route match.
   * phpcs:enable
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RouteMatchInterface $routeMatch) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->routeMatcher = $routeMatch;
  }

  /**
   * Create.
   * phpcs:disable
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   Container.
   * @param array $configuration
   *   Configuration.
   * @param string $plugin_id
   *   Plugin ID.
   * @param mixed $plugin_definition
   *   Plugin definitions.
   *
   * @return static
   *
   * phpcs:enable
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return ['label_display' => 'hidden'];
  }

  /**
   * Builds and returns the renderable array for this block plugin.
   *
   * If a block should not be rendered because it has no content, then this
   * method must also ensure to return no content: it must then only return an
   * empty array, or an empty array with #cache set (with cacheability metadata
   * indicating the circumstances for it being empty).
   *
   * @return array
   *   A renderable array representing the content of the block.
   *
   * @see \Drupal\block\BlockViewBuilder
   */
  public function build() {
    $build = [];
    // Try to get the node from the route.
    $node = $this->routeMatcher->getParameter('node');
    if ($this->routeMatcher->getRouteName() == 'entity.node.preview') {
      $node = $this->routeMatcher->getParameter('node_preview');
    }
    if ($node && $node instanceof NodeInterface) {
      $field_rendered_array = $node->get('field_sidebar_right_paragraphs')
        ->view('full');
      if (!$node->isPublished()) {
        $build['sidebar_paragraphs']['#markup'] = render($field_rendered_array);
      }
      else {
        $build['sidebar_paragraphs'] = $field_rendered_array;
      }
      $build['#cache'] = [
        'tags' => $this->getCacheTags(),
        'contexts' => $this->getCacheContexts(),
        'keys' => [
          'entity_view',
          'node',
          $node->id(),
          'default',
        ],
      ];
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $cache_tags = parent::getCacheTags();
    $node = $this->routeMatcher->getParameter('node');
    if ($node && $node instanceof NodeInterface) {
      $cache_tags[] = 'node:' . $node->id();
      $cache_tags[] = 'node_view';
    }
    return $cache_tags;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts(parent::getCacheContexts(), ['url.path', 'user.permissions']);
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    $node = $this->routeMatcher->getParameter('node');
    $condition = $node && $node instanceof NodeInterface &&
      $node->hasField('field_sidebar_right_paragraphs') &&
      !$node->get('field_sidebar_right_paragraphs')->isEmpty();
    return AccessResult::allowedIf($condition);
  }

}
