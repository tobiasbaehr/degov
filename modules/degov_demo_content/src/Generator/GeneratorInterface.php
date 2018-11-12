<?php

namespace Drupal\degov_demo_content\Generator;

interface GeneratorInterface {

  public function resetContent(): void;

  public function generateContent(): void;

  public function deleteContent(): void;

}
