<?php
// This file is for Bitbucket pipelines.

$file = 'composer.json';
$data = json_decode(file_get_contents($file), true);
$data["autoload-dev"]["psr-4"] = [
  "Drupal\\degov\\Behat\\Context\\" => "docroot/profiles/contrib/degov/testing/behat/context/",
  "Drupal\\degov\\Behat\\Context\\Traits\\" => "docroot/profiles/contrib/degov/testing/behat/context/Traits/",
];
file_put_contents('composer.json', json_encode($data, JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT));
