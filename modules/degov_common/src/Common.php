<?php

namespace Drupal\degov_common;

use Drupal\degov_theming\Service\Template;

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
   * @deprecated Use Drupal\degov_theming\Service\Template::suggestAndLoad() instead.
   */
  public static function addThemeSuggestions(array &$variables, $hook, array &$info, array $options) {
    /**
     * @var Template $template
     */
    $template = \Drupal::service('degov_theming.template');
    $template->suggest($variables, $hook,$info, $options);
  }

  public static function removeContent(array $options) : void {
    /* @var $entity_type string */
    /* @var $entity_bundles array */
    extract($options);

    if ($entity_type == 'paragraph') {
			$paragraphQuery = \Drupal::entityQuery('paragraph');

			foreach ($entity_bundles as $type) {
				$paragraphQuery->condition('type', $type);
			}

			$entity_ids = $paragraphQuery
				->execute();
			$controller = \Drupal::entityTypeManager()->getStorage($entity_type);
			$entities = $controller->loadMultiple($entity_ids);
			$controller->delete($entities);

			return;
		}

    foreach ($entity_bundles as $entity_bundle) {
      \Drupal::logger($entity_bundle)->notice(t('Removing all content of type @bundle', ['@bundle' => $entity_bundle]));
      $entity_ids = \Drupal::entityQuery($entity_type)
        ->condition('type', $entity_bundle)
        ->execute();
      $controller = \Drupal::entityTypeManager()->getStorage($entity_type);
      $entities = $controller->loadMultiple($entity_ids);
      $controller->delete($entities);
    }
  }

}
