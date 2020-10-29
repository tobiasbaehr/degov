<?php

// @codingStandardsIgnoreFile

$databases['default']['default'] = array(
  'database' => 'testing',
  'username' => 'testing',
  'password' => 'testing',
  'prefix' => '',
  'host' => getenv('CI_MYSQL_HOST')?: '127.0.0.1',
  'port' => getenv('CI_MYSQL_PORT')?: '3306',
  'namespace' => 'Drupal\\Core\\Database\\Driver\\mysql',
  'driver' => 'mysql',
);

$settings["file_private_path"] = "sites/default/files/private";
$settings["file_public_path"] = "sites/default/files";
$settings["file_temp_path"] = "/tmp";
$settings["trusted_host_patterns"] = ["^127.0.0.1$","^localhost$","^host.docker.internal$"];
$config["locale.settings"]["translation"]["path"] = "sites/default/files/translations";
$config["swiftmailer.transport"]["transport"] = "spool";
$config["swiftmailer.transport"]["spool_directory"] = "/tmp/swiftmailer-spool";
