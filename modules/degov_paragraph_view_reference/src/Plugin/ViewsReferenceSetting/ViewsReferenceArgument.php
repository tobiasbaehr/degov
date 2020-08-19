<?php

declare(strict_types=1);

namespace Drupal\degov_paragraph_view_reference\Plugin\ViewsReferenceSetting;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\user\Entity\User;
use Drupal\views\Entity\View;
use Drupal\views\ViewExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\viewsreference\Plugin\ViewsReferenceSetting\ViewsReferenceArgument as ViewsReferenceArgumentOrigin;

/**
 * Class ViewsReferenceArgument
 *
 * @package Drupal\degov_paragraph_view_reference\Plugin\ViewsReferenceSetting
 */
final class ViewsReferenceArgument extends ViewsReferenceArgumentOrigin implements ContainerFactoryPluginInterface {

  use StringTranslationTrait;

  /** @var \Drupal\Core\Entity\EntityTypeManagerInterface*/
  private $entityTypeManager;

  /**
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $currentRouteMatch;

  /**
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->currentRouteMatch = $container->get('current_route_match');
    $instance->token = $container->get('token');
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
    $display_id = 'default';
    if (!empty($this->configuration['display_id'])) {
      $display_id = $this->configuration['display_id'];
    }
    // Get the view display.
    $display = $view->getDisplay($display_id);
    // Contexual arguments if they are not overriden are set only in default.
    if (empty($display['display_options']['arguments'])) {
      // If no arguments found try to get the ones from default display.
      $display = $view->getDisplay('default');
    }
    $argument_values = [];
    $default_value = $form_field['#default_value'];
    $states = $form_field['#states'];
    if (is_string($default_value)) {
      $argument_values = explode('/', $default_value);
    }
    if (!empty($display['display_options']['arguments'])) {
      $element = [];
      $num = 0;
      foreach ($display['display_options']['arguments'] as $argument_name => $argument_value) {
        $title_extra = '';
        if (array_key_exists('not', $argument_value)) {
          $title_extra = ' (' . $this->t('Exclude this items') . ')';
        }
        if ($argument_name === 'tid' && $argument_value['table'] === 'taxonomy_index') {
          $element[$num] = [
            '#type'               => 'entity_autocomplete',
            '#title'              => $this->t('Taxonomy term') . $title_extra,
            '#default_value'      => !empty($argument_values[$num]) ? Term::load($argument_values[$num]) : NULL,
            '#target_type'        => 'taxonomy_term',
            '#selection_handler'  => 'default:taxonomy_term',
            '#selection_settings' => ['match_limit' => 1],
          ];
          $num++;
          continue;
        }
        if ($argument_name === 'nid' && $argument_value['table'] === 'node_field_data') {
          $element[$num] = [
            '#type'               => 'entity_autocomplete',
            '#title'              => $this->t('Node') . $title_extra,
            '#default_value'      => !empty($argument_values[$num]) ? Node::load($argument_values[$num]) : NULL,
            '#target_type'        => 'node',
            '#selection_handler'  => 'default:node',
            '#selection_settings' => ['match_limit' => 1],
          ];
          $num++;
          continue;
        }
        if ($argument_name === 'uid' && $argument_value['table'] === 'node_field_data') {
          $element[$num] = [
            '#type'               => 'entity_autocomplete',
            '#title'              => $this->t('User') . $title_extra,
            '#default_value'      => !empty($argument_values[$num]) ? User::load($argument_values[$num]) : NULL,
            '#target_type'        => 'user',
            '#selection_handler'  => 'default:user',
            '#selection_settings' => ['match_limit' => 1],
          ];
          $num++;
          continue;
        }
        // Get field info.
        $info = $this->viewsArgumentFieldInfo($argument_value);
        $field_info = $info['info'];
        $bundle_info = $info['bundle_info'];
        if ($field_info) {
          $element[$num] = [
            '#type' => ($field_info->getType() === 'entity_reference') ? 'entity_autocomplete' : 'textfield',
            '#title' => empty($bundle_info) ? $field_info->getLabel() : $bundle_info->getLabel(),
            '#description' => empty($bundle_info) ? $field_info->getDescription() : $bundle_info->getDescription(),
            '#default_value' => $argument_values[$num] ?? '',
          ];
          $element[$num]['#title'] .= $title_extra;

          // If it is entity reference and some more settings.
          if (($field_info->getType() === 'entity_reference')) {
            $info_settings = $field_info->getSettings();
            $bundle_settings = $bundle_info->getSettings();
            $element[$num]['#target_type'] = $info_settings['target_type'];
            $element[$num]['#selection_handler'] = $bundle_settings['handler'];
            $element[$num]['#selection_settings'] = ['match_limit' => 1];
            $element[$num]['#selection_settings']['target_bundles'] = $bundle_settings['handler_settings']['target_bundles'];
            // Default value could be only entity, let's load one.
            $entity_storage = $this->entityTypeManager
              ->getStorage($info_settings['target_type']);
            $entity = $entity_storage->load($element['argument'][$num]['#default_value']);
            $element[$num]['#default_value'] = $entity ?? '';
          }
        }
        else {
          $element[$num] = [
            '#type' => 'textfield',
            '#title' => $argument_value['field'],
            '#default_value' => $argument_values[$num] ?? '',
          ];
        }
        $num++;
      }
      if (count($element) > 0) {
        $form = [
          '#type' => 'details',
          '#title' => $this->t('View arguments'),
          '#states' => $states,
        ] + $element;
        $form_field = $form;
      }
    }
  }

  /**
   * @inheritDoc
   */
  public function alterView(ViewExecutable $view, $value) {

    if (!empty($value)) {
      $arguments_options = $view->getDisplay()->getOption('arguments');
      if (!empty($arguments_options)) {
        $view_arguments_config = array_values($arguments_options);
      }

      // Load the arguments from the field storage.
      $arguments = [$value];
      if (preg_match('/\//', $value)) {
        $arguments = explode('/', $value);
      }
      /** @var \Drupal\node\NodeInterface $node */
      $node = $this->currentRouteMatch->getParameter('node');
      if (is_array($arguments)) {
        foreach ($arguments as $index => $argument) {
          // Check if there are any tokens that need to be replaced.
          if (!empty($this->token->scan($argument))) {
            $arguments[$index] = $this->token->replace($argument, ['node' => $node]);
          }
          // If the argument is not set in the field set the exception value.
          if ($argument == '' && !empty($view_arguments_config[$index])) {
            if (!empty($view_arguments_config[$index]['exception']['value'])) {
              $arguments[$index] = $view_arguments_config[$index]['exception']['value'];
            }
            else {
              $arguments[$index] = 0;
            }
            // If there is a default value for the node, set it - we have
            // the node object.
            if ($view_arguments_config[$index]['default_argument_type'] === 'node' && $node instanceof NodeInterface) {
              $arguments[$index] = $node->id();
            }
          }
        }
      }
      $view->setArguments($arguments);
    }
  }

  private function viewsArgumentFieldInfo(array $argument): array {
    $info = FALSE;
    if (!empty($argument['table'])) {
      $keys = explode('__', $argument['table']);
      if (!empty($keys)) {
        $info = FieldStorageConfig::loadByName($keys[0], $keys[1]);
        // If it is entity reference field try to get the target type and selector settings.
        if ($info && $info->getType() === 'entity_reference') {
          $bundles = $info->getBundles();
          $bundles_machine_names = array_keys($bundles);
          $bundle_info = FieldConfig::loadByName($keys[0], $bundles_machine_names[0], $keys[1]);
        }
        else {
          $bundle_info = [];
        }
      }
    }
    return ['info' => $info, 'bundle_info' => $bundle_info];
  }

}
