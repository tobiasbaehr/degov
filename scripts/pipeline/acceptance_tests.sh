#!/usr/bin/env bash

# Unset variables
set -o nounset
set -o pipefail
# hard fail
set -o errexit

ln -sf $BITBUCKET_CLONE_DIR/php_error.log /tmp/php_error.log

COMPOSER_EXIT_ON_PATCH_FAILURE=1
export COMPOSER_EXIT_ON_PATCH_FAILURE
COMPOSER_MEMORY_LIMIT=-1
export COMPOSER_MEMORY_LIMIT
CI_ROOT_DIR=$BITBUCKET_CLONE_DIR
export CI_ROOT_DIR

_info() {
  local color_info="\\x1b[32m"
  local color_reset="\\x1b[0m"
  echo -e "$(printf '%s%s%s\n' "$color_info" "$@" "$color_reset")"
}

_drush() {
  COLUMNS=$(tput cols 2>/dev/null) bin/drush --yes --ansi "$@"
}

_update_translations() {
  _info "### Update translations"
  _drush locale:check \
  && _drush locale:update
  _info "### Clear cache"
  _drush cr
}

_drush_watchdog() {
  _info "### Show watchdog"
  _drush ws --extended --count 500
  _drush watchdog:delete all
}

_composer() {
  composer --ansi --profile "$@"
}

_info "### Wait for packagist"
doWhile="0"
while [ $doWhile -eq "0" ]; do
   GREP=`wget -q -O - https://packagist.org/packages/degov/degov | grep ">dev-$BITBUCKET_BRANCH<"`
   if [ ! -z "$GREP" ]; then
        doWhile=1
   fi
   sleep 1
done

docker run --name mysql-$1 -e MYSQL_USER=testing -e MYSQL_PASSWORD=testing -e MYSQL_DATABASE=testing -p 3306:3306 -d mysql/mysql-server:5.7 --max_allowed_packet=1024M

_info "### Setting up project folder"
_composer create-project --no-progress degov/degov-project --no-install
cd degov-project
rm composer.lock
_info "### Install profile"
_composer require --no-progress "degov/degov:dev-$BITBUCKET_BRANCH#$BITBUCKET_COMMIT" --update-with-dependencies
PATH="$(pwd)/bin/:$PATH"
export PATH

(cd docroot && screen -dmS php-server php -S 0.0.0.0:80 .ht.router.php)
_info "### Configuring drupal"
_info '### Setting file system paths'
echo '$settings["file_private_path"] = "sites/default/files/private";' >> docroot/sites/default/settings.php
echo '$settings["file_public_path"] = "sites/default/files";' >> docroot/sites/default/settings.php
echo '$config["system.file"]["path"]["temporary"] = "/tmp";' >> docroot/sites/default/settings.php
echo '$settings["trusted_host_patterns"] = ["^127.0.0.1$","^localhost$","^host.docker.internal$"];' >> docroot/sites/default/settings.php
echo '$config["locale.settings"]["translation"]["path"] = "sites/default/files/translations";' >> docroot/sites/default/settings.php
echo '$config["swiftmailer.transport"]["transport"] = "spool";' >> docroot/sites/default/settings.php
echo '$config["swiftmailer.transport"]["spool_directory"] = "/tmp/swiftmailer-spool";' >> docroot/sites/default/settings.php

_info '### Creating file system folders'
mkdir -p docroot/sites/default/files/private/
mkdir docroot/sites/default/files/translations/
chmod 777 -R docroot/sites/default/files/

_info "### Setting up Behat"
mv docroot/profiles/contrib/degov/testing/behat/behat-no-drupal.dist.yml .
mv docroot/profiles/contrib/degov/testing/behat/behat.dist.yml .

_info "### Setup database by new installation or database dump"

if [[ "$2" == "install" ]]; then
    _info "### Installing a new"
    behat --format=pretty --out=std --format=junit --out=$BITBUCKET_CLONE_DIR/test-reports/ -c behat-no-drupal.dist.yml --strict --colors
    _update_translations
    _drush_watchdog
fi

if [[ "$2" == "db_dump" ]]; then
    cp docroot/profiles/contrib/degov/testing/behat/template/settings.local.php docroot/sites/default/settings.local.php
    sed -i 's/{{ mysql_auth.db }}/testing/g' docroot/sites/default/settings.local.php
    sed -i 's/{{ mysql_auth.user }}/testing/g' docroot/sites/default/settings.local.php
    sed -i 's/{{ mysql_auth.password }}/testing/g' docroot/sites/default/settings.local.php
    sed -i 's/{{ mysql_host }}/127.0.0.1/g' docroot/sites/default/settings.local.php
    echo '$settings["hash_salt"] = "7asdiugasd8f623gjwgasgf7a8stfasjdfsdafasdfasdfasdf";' >> docroot/sites/default/settings.local.php

    _info "### Drop any existing db"
    _drush sql:drop
    _info "### Importing db dump"
    zcat docroot/profiles/contrib/degov/testing/behat/degov-7.x-dev.sql.gz | docker exec -i mysql-$1 mysql -utesting -ptesting testing
    _info "### Clear cache"
    _drush cr
    _info "### Delete old watchdog entries from db dump"
    _drush watchdog:delete all
    _info "### Run database updates"
    _drush updb
    _info "### Re-install the degov_demo_content"
    _drush pm:uninstall degov_demo_content
    _drush en degov_demo_content
    _update_translations
    _drush_watchdog
fi

if [[ "$1" == "smoke_tests" ]]; then
    _info "### Running Behat smoke tests"
    # The installation sets admin/password as login data, therefore we reset the data here to match with the behat config.
    _drush upwd admin admin
    set +e
    behat --format=pretty --out=std --format=junit --out=$BITBUCKET_CLONE_DIR/test-reports/ -c behat.dist.yml --suite=smoke-tests --strict --colors
    EXIT_CODE=$?
    _drush_watchdog
    exit $EXIT_CODE

elif [[ "$1" == "backstopjs" ]]; then
    _info "### Running BackstopJS test"
    _info "### Set the Development Mode"
    _drush en degov_devel
    _drush config:set degov_devel.settings dev_mode true
    # Get Backstop pipeline output on non-zero test results.
    #   Storing script error state.
    #   See https://unix.stackexchange.com/a/310963
    ERROR_STATE="$(set +o); set -$-"
    set +e
    (cd docroot/profiles/contrib/degov/testing/backstopjs && docker run --rm --add-host host.docker.internal:$BITBUCKET_DOCKER_HOST_INTERNAL -v $(pwd):/src backstopjs/backstopjs test)
    BACKSTOP_EXIT_CODE=$?
    set +vx; eval "$ERROR_STATE"

    if [[ $BACKSTOP_EXIT_CODE -gt "0" ]]; then
      _info "### Dumping BackstopJS output"
      (cd $BITBUCKET_CLONE_DIR/degov-project/docroot/profiles/contrib/degov/testing/ && tar zfpc backstopjs.tar.gz backstopjs/ && mv backstopjs.tar.gz $BITBUCKET_CLONE_DIR)
      _info "### Approving changes"
      (cd docroot/profiles/contrib/degov/testing/backstopjs && docker run --rm --add-host host.docker.internal:$BITBUCKET_DOCKER_HOST_INTERNAL -v $(pwd):/src backstopjs/backstopjs approve)
      _info "### Re-test"
      (cd docroot/profiles/contrib/degov/testing/backstopjs && docker run --rm --add-host host.docker.internal:$BITBUCKET_DOCKER_HOST_INTERNAL -v $(pwd):/src backstopjs/backstopjs test)
      RC=$?
      if [[ "$RC" = 0 ]];then
        _info "BackstopJS test with the source files was failed. But new updated bitmaps_reference are provided in the artifacts download. Which was already succesfully re-tested."
        (cd $BITBUCKET_CLONE_DIR/degov-project/docroot/profiles/contrib/degov/testing/backstopjs/backstop_data && tar zfpc bitmaps_reference.tar.gz bitmaps_reference/ && mv bitmaps_reference.tar.gz $BITBUCKET_CLONE_DIR)
      else
        _info "### Dumping re-tested BackstopJS output"
        (cd $BITBUCKET_CLONE_DIR/degov-project/docroot/profiles/contrib/degov/testing/ && tar zfpc backstopjs.tar.gz backstopjs/ && mv backstopjs.tar.gz $BITBUCKET_CLONE_DIR)
      fi
      exit $BACKSTOP_EXIT_CODE
    fi

elif [[ "$1" != "backstopjs" ]]; then
    _info "### Running Behat features by tags: $1"
    set +e
    behat --format=pretty --out=std --format=junit --out=$BITBUCKET_CLONE_DIR/test-reports/ -c behat.dist.yml --suite=default --tags="$1" --strict --colors
    EXIT_CODE=$?
    _drush_watchdog
    exit $EXIT_CODE
fi

# For debugging via db dump
#bin/drush sql:dump --gzip > $BITBUCKET_CLONE_DIR/$1-degov.sql.gz
