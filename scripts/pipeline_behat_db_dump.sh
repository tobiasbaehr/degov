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
echo "### Starting chrome container"
docker run -d --name="testing" -p 4444:4444 --net="host" -v "$BITBUCKET_CLONE_DIR/degov-project/docroot/profiles/contrib/degov/testing/fixtures:/home/headless/" -v $BITBUCKET_CLONE_DIR:$BITBUCKET_CLONE_DIR derh4nnes/selenium-chrome-headless
echo "Setting up project"
cp docroot/profiles/contrib/degov/testing/behat/composer-require-namespace.php .
php composer-require-namespace.php
composer dump-autoload
echo "### Configuring PHP"
(cd docroot && screen -dmS php-server php -c /etc/php/7.1/cli/php_more_upload.ini -S localhost:80 .ht.router.php)
export PATH="$HOME/.composer/vendor/bin:$PATH"
echo "### Configuring drupal"
cp docroot/profiles/contrib/degov/testing/behat/template/settings.local.php docroot/sites/default/settings.local.php
echo '### Setting connection to database'
sed -i 's/{{ mysql_auth.db }}/testing/g' docroot/sites/default/settings.local.php
sed -i 's/{{ mysql_auth.user }}/root/g' docroot/sites/default/settings.local.php
sed -i 's/{{ mysql_auth.password }}/testing/g' docroot/sites/default/settings.local.php
sed -i 's/{{ mysql_host }}/0.0.0.0/g' docroot/sites/default/settings.local.php
echo '### Deleting hash salt and setting new one'
echo "\$settings['hash_salt'] = 'P3QB9CRcjE7O2q8soMprrPzVhckOGnNefUl4Bz0G-JuNv5lYUxmevcfIDyRW_5uFd4B1DGB59g';" >> docroot/sites/default/settings.local.php
echo '$settings["file_private_path"] = "sites/default/files/private";' >> docroot/sites/default/settings.local.php
mkdir docroot/sites/default/files/
chmod 777 -R docroot/sites/default/files/
zcat docroot/profiles/contrib/degov/testing/behat/degov-6.3.x-dev.sql.gz | bin/drush sql:cli
echo "### Updating"
bin/drush cr && bin/drush updb -y && bin/drush locale-check && bin/drush locale-update && bin/drush en degov_demo_content -y && bin/drush dcreg -y
echo "### Running Behat tests"
mv docroot/profiles/contrib/degov/testing/behat/behat.yml .
behat
echo "### Running Behat smoke tests"
bin/drush upwd admin admin
mv docroot/profiles/contrib/degov/testing/behat/behat-smoke-tests.yml .
behat -c behat-smoke-tests.yml
