#!/usr/bin/env bash
set -e
PHPVERSION=$1
composer create-project degov/degov-project --no-install
cd degov-project
rm composer.lock
composer require "degov/degov:dev-$BITBUCKET_BRANCH#$BITBUCKET_COMMIT"
docker run -d --name="testing" -p 4444:4444 --net="host" -v "$BITBUCKET_CLONE_DIR/degov-project/docroot/profiles/contrib/degov/testing/fixtures:/home/headless/" -v $BITBUCKET_CLONE_DIR:$BITBUCKET_CLONE_DIR derh4nnes/selenium-chrome-headless
cp docroot/profiles/contrib/degov/testing/behat/composer-require-namespace.php .
php composer-require-namespace.php
rm docroot/modules/contrib/lightning_core/tests/contexts/WatchdogContext.behat.inc
composer dump-autoload
(cd docroot && screen -dmS php-server php -c /etc/php/$PHPVERSION/cli/php_more_upload.ini -S localhost:80 .ht.router.php)
export PATH="$HOME/.composer/vendor/bin:$PATH"
phpstan analyse docroot/profiles/contrib/degov -c docroot/profiles/contrib/degov/phpstan.neon --level=1 || true
(cd docroot/profiles/contrib/degov && phpunit --testdox)
cp docroot/profiles/contrib/degov/testing/behat/template/settings.local.php docroot/sites/default/settings.local.php
sed -i 's/{{ mysql_auth.db }}/testing/g' docroot/sites/default/settings.local.php
sed -i 's/{{ mysql_auth.user }}/root/g' docroot/sites/default/settings.local.php
sed -i 's/{{ mysql_auth.password }}/testing/g' docroot/sites/default/settings.local.php
sed -i 's/localhost/127.0.0.1/g' docroot/sites/default/settings.local.php
echo '$settings["file_private_path"] = "sites/default/files/private";' >> docroot/sites/default/settings.local.php
mkdir docroot/sites/default/files/
chmod 777 -R docroot/sites/default/files/
mv docroot/profiles/contrib/degov/testing/behat/behat-no-drupal.yml .
behat -c behat-no-drupal.yml -vvv
bin/drush locale-check && bin/drush locale-update && bin/drush cr
mv docroot/profiles/contrib/degov/testing/behat/behat.yml .
behat
