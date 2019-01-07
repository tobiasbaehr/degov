<?php
// This file is for Bitbucket pipelines.

$file = 'composer.json';
$data = json_decode(file_get_contents($file), TRUE);
$data['autoload-dev']['psr-4'] = [
  "Drupal\\degov\\Behat\\Context\\" => 'docroot/profiles/contrib/degov/testing/behat/context/',
  "Drupal\\degov\\Behat\\Context\\Traits\\" => 'docroot/profiles/contrib/degov/testing/behat/context/Traits/',
  "Drupal\\Tests\\lightning_media\\" => 'docroot/profiles/contrib/degov/modules/lightning_media/tests/src',
];
$data['autoload']['classmap'][] = 'docroot/profiles/contrib/degov/modules/lightning_core/tests/contexts/AwaitTrait.inc';
file_put_contents('composer.json', json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
