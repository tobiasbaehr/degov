<?php

declare(strict_types=1);

namespace Drupal\degov_paragraph_view_reference\Plugin\ViewsReferenceSetting;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Utility\Html;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\views\Entity\View;
use Drupal\views\ViewExecutable;
use Drupal\viewsreference\Plugin\ViewsReferenceSettingInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * The views reference setting limit results plugin.
 *
 * @ViewsReferenceSetting(
 *   id = "view_mode",
 *   label = @Translation("Views row view mode"),
 *   default_value = "",
 * )
 */
final class ViewsReferenceViewMode extends PluginBase implements ViewsReferenceSettingInterface, ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /** @var \Drupal\Core\Entity\EntityDisplayRepository*/
  private $entityDisplayRepository;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->entityDisplayRepository = $container->get('entity_display.repository');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function alterFormField(&$form_field): void {
    if (empty($this->configuration['view_name']) || $this->configuration['view_name'] === '_none') {
      return;
    }

    /** @var \Drupal\views\Entity\View $view */
    $view = View::load($this->configuration['view_name']);
    $entity_type = $view->getExecutable()->getBaseEntityType()->id();
    $view_modes = ['' => $this->t('As defined in the view')];

    $allowedViewModes = [
      'small_image',
      'long_text',
      'slim',
      'preview',
    ];
    $view_modes += array_filter($this->entityDisplayRepository->getViewModeOptions($entity_type), function (string $view_mode) use ($allowedViewModes) {
      return in_array($view_mode, $allowedViewModes);
    }, ARRAY_FILTER_USE_KEY);

    $form_field['#type'] = 'select';
    $form_field['#options'] = $view_modes;
    $form_field['#attributes'] = ['class' => ['viewsreference_view_mode']];
  }

  /**
   * {@inheritdoc}
   */
  public function alterView(ViewExecutable $view, $value) {
    // If the view mode is set in field settings set it for the view display.
    if (!empty($value)) {
      if ($view->rowPlugin && !$view->rowPlugin->usesFields() && !empty($view->rowPlugin->options['view_mode'])) {
        $view->rowPlugin->options['view_mode'] = $value;
        // Add view mode to the cache keys, so the renderable array
        // could be safely cached.
        $view->element['#cache']['keys'][] = $value;
      }
      $css_class = $view->display_handler->getOption('css_class');
      $new_css_class = $css_class . ' ' . Html::cleanCssIdentifier($value);
      $view->display_handler->setOption('css_class', $new_css_class);

      if ($view->style_plugin && $view->style_plugin->usesRowPlugin()) {
        $view->style_plugin->options['row_class'] .= ' ' . Html::cleanCssIdentifier($value);
      }
    }
  }

}
