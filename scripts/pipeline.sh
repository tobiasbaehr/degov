#!/usr/bin/env bash
set -e

PHPVERSION=7.2

echo "### Executing Pipeline script with PHP: $PHPVERSION"
echo "### Setting up project folder"
composer create-project degov/degov-project --no-install
cd degov-project
rm composer.lock
composer require "degov/degov:dev-$BITBUCKET_BRANCH#$BITBUCKET_COMMIT" weitzman/drupal-test-traits:1.0.0-alpha.1 --update-with-dependencies

echo "Setting up project"
cp docroot/profiles/contrib/degov/testing/behat/composer-require-namespace.php .
php composer-require-namespace.php
composer dump-autoload
echo "### Configuring PHP"
(cd docroot && php -c /etc/php/$PHPVERSION/cli/php_more_upload.ini -S 0.0.0.0:80 .ht.router.php 2>/dev/null&)
export PATH="$HOME/.composer/vendor/bin:$PATH"
echo "### Checking code standards"
phpstan analyse docroot/profiles/contrib/degov -c docroot/profiles/contrib/degov/phpstan.neon --level=1 || true
echo "### Running PHPUnit and KernelBase tests"
(cd docroot/profiles/contrib/degov && phpunit --testdox)
echo "### Configuring drupal"
cp docroot/profiles/contrib/degov/testing/behat/template/settings.local.php docroot/sites/default/settings.local.php
sed -i 's/{{ mysql_auth.db }}/testing/g' docroot/sites/default/settings.local.php
sed -i 's/{{ mysql_auth.user }}/root/g' docroot/sites/default/settings.local.php
sed -i 's/{{ mysql_auth.password }}/testing/g' docroot/sites/default/settings.local.php
sed -i 's/{{ mysql_host }}/mysql/g' docroot/sites/default/settings.local.php
echo '$settings["file_private_path"] = "sites/default/files/private";' >> docroot/sites/default/settings.local.php
mkdir docroot/sites/default/files/
chmod 777 -R docroot/sites/default/files/
echo "### Setting up Behat"
mv docroot/profiles/contrib/degov/testing/behat/behat.yml .
echo "### Installing drupal with Behat"
behat --suite=no-drupal --strict
mkdir /app || true
bin/drush sql-dump --gzip --result-file=/app/dump.sql

echo "### Updating translation"
bin/drush locale-check && bin/drush locale-update && bin/drush cr

echo "### Running Behat tests"
behat --suite=default --strict

echo "### Running Behat smoke tests"
bin/drush upwd admin admin
behat --suite=smoke-tests --strict

# echo "### Running Behat upload tests"
# behat --suite=tests-with-file-upload --strict
