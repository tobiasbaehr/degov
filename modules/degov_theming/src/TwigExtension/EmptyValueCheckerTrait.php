<?php

namespace Drupal\degov_theming\TwigExtension;


trait EmptyValueCheckerTrait {

  public function isNotEmpty($build, string $stripTags = ''): bool {
    $build = $this->renderVar($build);
    $build = strip_tags($build, $stripTags);
    try {
      $build = \trim($build, " \t\n\r\0\x0B");
      $build = \trim($build, ' \t\n\r\0\x0B');
    } catch (\Exception $e) {
      $this->logger->error($e->getMessage());
    }
    if (!empty($build)) {
      return true;
    }
    return false;
  }

  public function isEmpty($build, string $stripTags = ''): bool {
    if (!$this->isNotEmpty($build, $stripTags)) {
      return true;
    }

    return false;
  }

}