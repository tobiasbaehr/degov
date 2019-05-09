#!/usr/bin/env bash
set -e
PHPVERSION=$1

echo "### Executing Pipeline script with PHP: $PHPVERSION"
echo "### Setting up project folder"

echo "### Wait for packagist"
doWhile="0"
while [ $doWhile -eq "0" ]; do
   GREP=`wget -q -O - https://packagist.org/packages/degov/degov | grep ">dev-$BITBUCKET_BRANCH<"`
   if [ ! -z "$GREP" ]; then
        doWhile=1
   fi
   sleep 1
done

composer create-project degov/degov-project --no-install
cd degov-project
rm composer.lock
composer require "degov/degov:dev-$BITBUCKET_BRANCH#$BITBUCKET_COMMIT" weitzman/drupal-test-traits:1.0.0-alpha.1 --update-with-dependencies
echo "Setting up project"
cp docroot/profiles/contrib/degov/testing/behat/composer-require-namespace.php .
php composer-require-namespace.php
rm composer-require-namespace.php
cp docroot/profiles/contrib/degov/scripts/Robo/composer-require-namespace.php .
php composer-require-namespace.php
composer dump-autoload
rm composer-require-namespace.php
export PATH="$HOME/.composer/vendor/bin:$PATH"
echo "### Checking code standards"
phpstan analyse docroot/profiles/contrib/degov -c docroot/profiles/contrib/degov/phpstan.neon --level=1 || true
echo "### Running PHPUnit and KernelBase tests"
(cd docroot/profiles/contrib/degov && phpunit --testdox)
