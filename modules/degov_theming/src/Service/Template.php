<?php

namespace Drupal\degov_theming\Service;

use Drupal\Core\Asset\LibraryDiscovery;
use Drupal\Core\Template\TwigEnvironment;
use Drupal\Core\Theme\ThemeManager;
use Drupal\degov_theming\Facade\ComponentLocation;
use Symfony\Component\Filesystem\Filesystem;

class Template {

  /**
   * @var ThemeManager
   */
  private $themeManager;

  /**
   * @var LibraryDiscovery
   */
  private $libraryDiscovery;

  /**
   * @var DrupalPath
   */
  private $drupalPath;

  /**
   * @var Filesystem
   */
  private $filesystem;

  /**
   * @var TwigEnvironment
   */
  private $twig;

  public function __construct(
    ThemeManager $themeManager, ComponentLocation $componentLocation, TwigEnvironment $twig
  )
  {
    $this->themeManager = $themeManager;
    $this->libraryDiscovery = $componentLocation->getLibraryDiscovery();
    $this->filesystem = $componentLocation->getFilesystem();
    $this->drupalPath = $componentLocation->getDrupalPath();
    $this->twig = $twig;
  }

  private function getInheritedTheme() {
    $activeTheme = $this->themeManager->getActiveTheme();
    $baseThemes = $activeTheme->getBaseThemes();

    return array_shift($baseThemes);
  }

  public function suggest(array &$variables, $hook, array &$info, array $options) {
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
      $path_to_theme = $this->themeManager->getActiveTheme()->getPath();

      if (strpos($template_path, 'themes/contrib') === 0 ||
        (strpos($template_path, $path_to_theme) === FALSE && strpos($template_path, $this->getInheritedTheme()->getPath()) === FALSE)) {
        list($variables, $template_filename) = $this->computeTemplateFilename($variables, $entity_view_modes, $entity_type, $entity_bundle);

        $module_path = $this->drupalPath->getPath('module', $module_name);
        $template_fullname = $module_path . '/templates/' . $template_filename . '.html.twig';
        if ($this->filesystem->exists($template_fullname)) {
          $info['template'] = $template_filename;
          $info['theme path'] = "modules";
          $info['path'] = $module_path . '/templates';
        }

      }
    }
    // Include defined entity bundle libraries.
    if (isset($entity_bundle)) {
      $library = $this->libraryDiscovery->getLibraryByName($module_name, $entity_bundle);
      if ($library) {
        $variables['#attached']['library'][] = $module_name . '/' . $entity_bundle;
      }
    }
  }

  private function computeTemplateFilename(array &$variables, $entity_view_modes, $entity_type, $entity_bundle): array
  {
    if (isset($variables['elements']['#view_mode'])
      && in_array($variables['elements']['#view_mode'], $entity_view_modes)) {
      $template_filename = $entity_type . '--' . $entity_bundle . '--' . $variables['elements']['#view_mode'];
    } else {
      if (isset($entity_bundle)) {
        $template_filename = $entity_type . '--' . $entity_bundle . '--default';
      } else {
        $template_filename = $entity_type . '--default';
      }
    }
    return [
      $variables,
      $template_filename
    ];
  }

  public function render(string $module, string $templatePath, array $variables = []) {
    $path = $this->drupalPath->getPath('module', $module) . '/' . $templatePath;
    $twigTemplate = $this->twig->load($path);

    return $twigTemplate->render($variables);
  }

}