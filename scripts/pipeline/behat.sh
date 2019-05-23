#!/bin/bash

# Temp! Do not fail at first error
#set -e

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

docker run --name mysql-$1 -e MYSQL_USER=testing -e MYSQL_PASSWORD=testing -e MYSQL_DATABASE=testing -p 3306:3306 -d mysql/mysql-server:5.7 --max_allowed_packet=1024M

composer create-project degov/degov-project --no-install degov-project
cd degov-project
rm composer.lock
composer require "degov/degov:dev-$BITBUCKET_BRANCH#$BITBUCKET_COMMIT" weitzman/drupal-test-traits:1.0.0-alpha.1 --update-with-dependencies
echo "Setting up project"
cp docroot/profiles/contrib/degov/testing/behat/composer-require-namespace.php .
php composer-require-namespace.php
composer dump-autoload
echo "### Configuring PHP"
(cd docroot && screen -dmS php-server php -d memory_limit=256M -d error_log=$BITBUCKET_CLONE_DIR/php_error.log -c /etc/php/7.1/cli/php_more_upload.ini -S 0.0.0.0:80 .ht.router.php)
export PATH="$HOME/.composer/vendor/bin:$PATH"
echo "### Configuring drupal"
echo '### Setting file system paths'
echo '$settings["file_private_path"] = "sites/default/files/private";' >> docroot/sites/default/settings.php
echo '$settings["file_public_path"] = "sites/default/files";' >> docroot/sites/default/settings.php
echo '$config["system.file"]["path"]["temporary"] = "/tmp";' >> docroot/sites/default/settings.php
echo '### Creating file system folders'
mkdir docroot/sites/default/files/
mkdir docroot/sites/default/files/private/
chmod 777 -R docroot/sites/default/files/
echo "### Setting up Behat"
mv docroot/profiles/contrib/degov/testing/behat/behat-no-drupal.yml .
mv docroot/profiles/contrib/degov/testing/behat/behat.yml .

echo "### Setup database by new installation or database dump"

if [[ "$2" == "install" ]]; then
    echo "### Installing anew"
    behat -c behat-no-drupal.yml -vvv
fi

if [[ "$2" == "db_dump" ]]; then
    cp docroot/profiles/contrib/degov/testing/behat/template/settings.local.php docroot/sites/default/settings.local.php
    sed -i 's/{{ mysql_auth.db }}/testing/g' docroot/sites/default/settings.local.php
    sed -i 's/{{ mysql_auth.user }}/testing/g' docroot/sites/default/settings.local.php
    sed -i 's/{{ mysql_auth.password }}/testing/g' docroot/sites/default/settings.local.php
    sed -i 's/{{ mysql_host }}/127.0.0.1/g' docroot/sites/default/settings.local.php
    echo '$settings["install_profile"] = "degov";' >> docroot/sites/default/settings.local.php
    echo '$settings["hash_salt"] = "7asdiugasd8f623gjwgasgf7a8stfasjdfsdafasdfasdfasdf";' >> docroot/sites/default/settings.local.php
    echo "### Drop any existing db"
    bin/drush sql:drop -y
    echo "### Importing db dump"
    zcat docroot/profiles/contrib/degov/testing/behat/degov-7.x-dev.sql.gz | docker exec -i mysql-$1 mysql -utesting -ptesting testing
    echo "### Updating"
    bin/drush cr && bin/drush updb -y && bin/drush locale-check && bin/drush locale-update && bin/drush pm:uninstall degov_demo_content -y && bin/drush en degov_demo_content -y
fi

echo "### Updating translation"
bin/drush locale-check && bin/drush locale-update && bin/drush cr

if [[ "$1" == "smoke_tests" ]]; then
    echo "### Running Behat smoke tests"
    bin/drush upwd admin admin
    bin/drush watchdog:delete all -y
    behat -c behat.yml --suite=smoke-tests --strict
else
    echo "### Running Behat features by tags"
    behat -c behat.yml --suite=default --tags="$1" --strict
fi

bin/drush sql:dump > $BITBUCKET_CLONE_DIR/$1-degov.sql && gzip $BITBUCKET_CLONE_DIR/$1-degov.sql