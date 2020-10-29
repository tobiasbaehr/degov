#!/usr/bin/env bash

set -o nounset
set -o pipefail
set -o errexit

if [[ -n ${DEBUG:-} ]]; then
  set -o xtrace
fi

# shellcheck disable=SC2164
__DIR__="$(
  cd "$(dirname "${0}")"
  pwd
)"

# shellcheck source=.
source "$__DIR__/.env"
if [[ -n ${DEBUG:-} ]]; then
  set -o xtrace
fi

# shellcheck source=.
source "$__DIR__/common_functions.sh"

main() {
  cd "$CI_ROOT_DIR/project"
  _setup_settings_php
  _setup_file_system
  _setup_local_settings_php
  _info "### Importing db dump"
  _drush sql-query --file="$TEST_DIR/lfs_data/$CONTRIBNAME-stable-$DB_DUMP_VERSION.sql.gz"
  _info "### Clear cache"
  _drush cr
  _copy_assets
  _info "### Delete old watchdog entries from db dump"
  _drush watchdog:delete all
  _info "### Run database updates"
  _drush updb
  _info "### Clear cache"
  _drush cr
  _disable_geocoder_presave
  _info "### Install the degov_demo_content"
  _drush en degov_demo_content
  _update_translations
  _drush_watchdog
}

main
