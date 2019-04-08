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

    $entity_type = $options['entity_type'];
    $entity_bundles = $options['entity_bundles'];
    $module_name = $options['module_name'];
    $entity_view_modes = $options['entity_view_modes'];

    $add_suggestion = FALSE;

    if ($hook === $entity_type) {
      // Add module overwritten template suggestions for only the entity bundles that are defined.
      if ($entity_bundles) {
        if ($hook === 'media') {
          $entity = $variables['elements']['#media'];
        } elseif ($hook === 'taxonomy_term') {
          $entity = $variables['term'];
        } else {
          $entity = $variables[$entity_type];
        }
        $entity_bundle = $entity->bundle();
        // Overwrite the core/contrib template with our module template in case no custom theme has overwritten the template.
        if (\in_array($entity_bundle, $entity_bundles, TRUE)) {
          $add_suggestion = TRUE;
        }
      } else {
        // In case no entity bundles are defined, we still include the default template override.
        $add_suggestion = TRUE;
      }
    }

    if ($add_suggestion) {
      $template_path = $info['theme path']; #substr($info['theme path'], 0, 14);
      $path_to_active_theme = $this->themeManager->getActiveTheme()->getPath();

      if (strpos($template_path, 'profiles/contrib') === 0 ||
        strpos($template_path, 'themes/contrib') === 0 ||
        (strpos($template_path, $path_to_active_theme) === FALSE && strpos($template_path, $this->getInheritedTheme()->getPath()) === FALSE)) {
        list($variables, $template_filename) = $this->computeTemplateFilename($variables, $entity_view_modes, $entity_type, $entity_bundle ?? NULL);
        // does the template exist in the active theme?
        $theme_templates_dirname = $this->buildPath($path_to_active_theme, 'templates');
        $template_found = $this->addTemplateToArrayIfFileIsFound($info, 'themes', $template_filename, $theme_templates_dirname);
        if (!$template_found) {
          // no? does the template exist in a base theme?
          $base_themes = $this->themeManager->getActiveTheme()->getBaseThemes();
          foreach ($base_themes as $base_theme) {
            if ($base_theme->getPath() !== null) {
              $theme_templates_dirname = $this->buildPath($base_theme->getPath(), 'templates');
              if ($this->addTemplateToArrayIfFileIsFound($info, 'themes', $template_filename, $theme_templates_dirname)) {
                $template_found = TRUE;
                break;
              }
            }
          }
        }
        if (!$template_found) {
          // no? does the template exist in a module?
          $module_path = $this->drupalPath->getPath('module', $module_name);
          if ($module_path) {
            $module_templates_dirname = $this->buildPath($module_path, 'templates');
            $this->addTemplateToArrayIfFileIsFound($info, "modules", $template_filename, $module_templates_dirname);
          }
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

  private function buildPath(string $base, string $directory): string
  {
    if (!preg_match("/\/$/", $base)) {
      $base .= '/';
    }
    return $base . $directory;
  }

  private function addTemplateToArrayIfFileIsFound(array &$original_array, string $theme_path, string $template_filename, string $directory_name): bool
  {
    $template_filename_with_suffix = $template_filename . '.html.twig';
    if ($directory_iterator = $this->getFileSystemIteratorForDirectory($directory_name)) {
      foreach ($directory_iterator as $file_in_directory) {
        if ($file_in_directory->getFilename() === $template_filename_with_suffix) {
          $original_array = array_merge($original_array, [
            'template'   => $template_filename,
            'theme path' => $theme_path,
            'path'       => $this->getDirnameWithoutVfsProtocol($file_in_directory->getPathName()),
          ]);
          return true;
        }
      }
    }
    return false;
  }

  private function getDirnameWithoutVfsProtocol(string $path): string
  {
    $path = dirname($path);
    if (preg_match('/^vfs:\/\/\//', $path)) {
      $path = str_replace('vfs:///', '', $path);
    }
    return $path;
  }

  private function getFileSystemIteratorForDirectory(string $directory_name)
  {
    $directory_path = $directory_name;
    if (preg_match('/vfsStreamDirectory$/', get_class($this->filesystem))) {
      $directory_path = $this->filesystem->url() . '/' . $directory_name;
    }
    if (file_exists($directory_path) && is_dir($directory_path)) {
      return new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory_path));
    }
    return null;
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