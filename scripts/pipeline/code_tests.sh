#!/usr/bin/env bash
set -e

touch $BITBUCKET_CLONE_DIR/php_error.log
echo "log_errors = On" >> /etc/php/7.2/cli/php.ini
echo "error_log = $BITBUCKET_CLONE_DIR/php_error.log" >> /etc/php/7.2/cli/php.ini
echo "error_reporting = E_ALL" >> /etc/php/7.2/cli/php.ini

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
composer require "degov/degov:dev-$BITBUCKET_BRANCH#$BITBUCKET_COMMIT" --update-with-dependencies
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
(cd docroot/profiles/contrib/degov && phpunit --testdox -vvv)
