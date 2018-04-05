<?php

namespace Drupal\degov_common;

use Drupal\Core\Controller\ControllerBase;
use Drupal\paragraphs\Entity\Paragraph;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class Common.
 *
 * Contains all common function implementations.
 *
 * @package Drupal\degov_common
 */
class Common {

  /**
   * Adds template suggestions and library implementations.
   *
   * Add this to the HOOK_preprocess() of your module. The first 3 arguments
   * are equal to the ones from the parent HOOK_preprocess().
   *
   * @param array &$variables
   *   Original $variables from the hook_preprocess() function.
   * @param string $hook
   *   Original $hook from the hook_preprocess() function.
   * @param array &$info
   *   Original $info from the hook_preprocess() function.
   * @param array $options
   *   A key named array of options, including:
   *   - module_name: mandatory value with the name of the module implementing the method.
   *   - entity_type: mandatory value with mostly the entity type created (E.g. node, paragraph, media, swiftmailer..)
   *   - entity_bundles: optional array of entity bundles created, could be empty.
   *   - entity_view_modes: optional array of entity view modes that need templates, could be empty.
   */
  public static function addThemeSuggestions(array &$variables, $hook, array &$info, array $options) {
    /* @var $entity_type string */
    /* @var $entity_bundles array */
    /* @var $module_name string*/
    /* @var $entity_view_modes array */
    extract($options);
    $add_suggestion = FALSE;

    if ($hook == $entity_type) {
      // Add module overwritten template suggestions for only the entity bundles that are defined.
      if ($entity_bundles) {
        if ($hook === 'media') {
          $entity = $variables['elements']['#media'];
        }
        elseif ($hook == 'taxonomy_term') {
          $entity = $variables['term'];
        }
        else {
          $entity = $variables[$entity_type];
        }
        $entity_bundle = $entity->bundle();
        // Overwrite the core/contrib template with our module template in case no custom theme has overwritten the template.
        if (in_array($entity_bundle, $entity_bundles)) {
          $add_suggestion = TRUE;
        }
      }
      else {
        // In case no entity bundles are defined, we still include the default template override.
        $add_suggestion = TRUE;
      }
    }

    if ($add_suggestion) {
      $template_path = $info['theme path']; #substr($info['theme path'], 0, 14);
      $path_to_theme = \Drupal::theme()->getActiveTheme()->getPath();
      // Only override templates that are defined by contrib modules.
      if (strpos($template_path, 'themes/contrib') === 0 || strpos($template_path, $path_to_theme) === FALSE) {
        // Add a template for every defined view mode else add it for the default view mode.
        if (isset($variables['elements']['#view_mode'])
          && in_array($variables['elements']['#view_mode'], $entity_view_modes)) {
            $template_filename = $entity_type . '--' . $entity_bundle . '--' . $variables['elements']['#view_mode'];
        }
        else {
          if (isset($entity_bundle)) {
            $template_filename = $entity_type . '--' . $entity_bundle . '--default';
          }
          else {
            $template_filename = $entity_type . '--default';
          }
        }
        $module_path = drupal_get_path('module', $module_name);
        $template_fullname = $module_path . '/templates/' . $template_filename . '.html.twig';
        if (file_exists($template_fullname )) {
          $info['template'] = $template_filename;
          $info['theme path'] = "modules";
          $info['path'] = $module_path . '/templates';
        }
      }
    }
    // Include defined entity bundle libraries.
    if (isset($entity_bundle)) {
      $library = \Drupal::service('library.discovery')->getLibraryByName($module_name, $entity_bundle);
      if ($library) {
        $variables['#attached']['library'][] = $module_name . '/' . $entity_bundle;
      }
    }
  }

  /**
   * Remove content of a given entity type and its bundles.
   *
   * @param array $options
   *   A key named array of options, including:
   *     - entity_type: entity type of the bundles.
   *     - entity_bundles: an array of entity bundle names.
   */
  public static function removeContent($options) {
    /* @var $entity_type string */
    /* @var $entity_bundles array */
    extract($options);
    // Retrieve the bundle name of the entity type.
    $entity_bundle_name = 'type';

    if ($entity_type == 'paragraph') {
			xdebug_break();

			$entityParagraphQuery = \Drupal::entityQuery('paragraphs_type');

//			foreach ($options['entity_bundles'] as $bundle) {
//				$entityParagraphQuery->condition($bundle);
//			}

			$ids = $entityParagraphQuery
				->condition('originalId', 'test')
				->execute();

			if (!empty($ids)) {
				$paragraphs = Paragraph::load;
				$storage->delete($paragraphs);
			}
		}
    foreach ($entity_bundles as $entity_bundle) {
      \Drupal::logger($entity_bundle)->notice(t('Removing all content of type @bundle', ['@bundle' => $entity_bundle]));
      $entity_ids = \Drupal::entityQuery($entity_type)
        ->condition($entity_bundle_name, $entity_bundle)
        ->execute();
      $controller = \Drupal::entityTypeManager()->getStorage($entity_type);
      $entities = $controller->loadMultiple($entity_ids);
      $controller->delete($entities);
    }
  }

}
