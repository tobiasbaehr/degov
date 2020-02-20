#!/usr/bin/env bash

set -o nounset
set -o pipefail
set -o errexit

if [[ -n "${DEBUG:-}" ]];then
  set -o xtrace
fi

# shellcheck disable=SC2164
__DIR__="$(cd "$(dirname "${0}")"; pwd)"

# shellcheck source=.
source "$__DIR__/.env"
# shellcheck source=.
source "$__DIR__/shared_scripts/common_functions.sh"

bash "$__DIR__/shared_scripts/start_services.sh" $1

_update_translations() {
  _info "### Update translations"
  _drush locale:check \
  && _drush locale:update
  _drush php-eval 'Drupal\degov\TranslationImporter::importForProfile()'
  _info "### Clear cache"
  _drush cr
}

cd project

_setup_settings_php
_setup_file_system

_info "### Setup database by new installation or database dump"

if [[ "$2" == "install" ]]; then
    _info "### Installing a new"
    _behat -c behat-no-drupal.dist.yml
    _update_translations
    _drush_watchdog
elif [[ "$2" == "db_dump" ]]; then
    _setup_local_settings_php
    _info "### Drop any existing db"
    _drush sql:drop
    _info "### Importing db dump"
    zcat $TEST_DIR/lfs_data/degov-7.x-dev.sql.gz | docker exec -i mysql-$1 mysql -utesting -ptesting testing
    _info "### Clear cache"
    _drush cr
    _info "### Delete old watchdog entries from db dump"
    _drush watchdog:delete all
    _info "### Run database updates"
    _drush updb
    _info "### Clear cache"
    _drush cr
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
    _behat -c behat.dist.yml --suite=smoke-tests
    EXIT_CODE=$?
    _drush_watchdog
    exit $EXIT_CODE

elif [[ "$1" == "html_validator" ]]; then
    _info "### Start HTML VALIDATION"
    bash $BITBUCKET_CLONE_DIR/scripts/pipeline/shared_scripts/html_validation.sh

elif [[ "$1" == "backstopjs" ]]; then
    bash $BITBUCKET_CLONE_DIR/scripts/pipeline/shared_scripts/backstopjs.sh

elif [[ "$1" != "backstopjs" ]]; then
    _info "### Running Behat features by tags: $1"
    set +e
    _behat -c behat.dist.yml --suite=default --tags="$1"
    EXIT_CODE=$?
    _drush_watchdog
    exit $EXIT_CODE
fi
