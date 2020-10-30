#!/usr/bin/env bash

set -o nounset
set -o pipefail
set -o errexit

if [[ -n ${DEBUG:-} ]]; then
  set -o xtrace
fi

_info() {
  local color_info='\x1b[96m'
  local color_reset='\x1b[0m'
  echo -e "$(printf '%s%s%s\n' "$color_info" "$@" "$color_reset")"
}

_err() {
  local color_error='\x1b[31m'
  local color_reset='\x1b[0m'
  echo -e "$(printf '%s%s%s\n' "$color_error" "$@" "$color_reset")" 1>&2
}

_drush() {
  COLUMNS=$(tput cols 2> /dev/null) drush --yes --ansi "$@"
}

_robo() {
  COLUMNS=$(tput cols 2> /dev/null) robo --ansi "$@"
}

_drush_watchdog() {
  _info "### Show watchdog"
  _drush ws --extended --count 500
  _drush watchdog:delete all
}

_backstopjs() {
  (cd "$TEST_DIR" \
    && docker run --add-host host.docker.internal:"$BITBUCKET_DOCKER_HOST_INTERNAL" -v "$(pwd)/backstopjs":/src -v "$(pwd)/lfs_data":/lfs_data backstopjs/backstopjs:5.0.2 "$@")
}

_behat() {
  _info "### Setting up Behat"
  mv -v "$TEST_DIR/behat/behat-no-drupal.dist.yml" .
  mv -v "$TEST_DIR/behat/behat.dist.yml" .
  behat --format=pretty --out=std --format=junit --out="$CI_ROOT_DIR/test-reports/" --strict --colors "$@"
}

_setup_settings_php() {
  _info "### Configuring settings.php"
  echo '$settings["file_private_path"] = "sites/default/files/private";' >> docroot/sites/default/settings.php
  echo '$settings["file_public_path"] = "sites/default/files";' >> docroot/sites/default/settings.php
  echo '$settings["file_temp_path"] = "/tmp";' >> docroot/sites/default/settings.php
  echo '$settings["trusted_host_patterns"] = ["^127.0.0.1$","^localhost$","^host.docker.internal$"];' >> docroot/sites/default/settings.php
  echo '$config["locale.settings"]["translation"]["path"] = "sites/default/files/translations";' >> docroot/sites/default/settings.php
  echo '$config["swiftmailer.transport"]["transport"] = "spool";' >> docroot/sites/default/settings.php
  echo '$config["swiftmailer.transport"]["spool_directory"] = "/tmp/swiftmailer-spool";' >> docroot/sites/default/settings.php
}

_setup_local_settings_php() {
  cp -v "$CI_ROOT_DIR"/scripts/ci/settings.local.php docroot/sites/default/settings.local.php
}

_setup_file_system() {
  _info '### Creating file system folders'
  mkdir -p docroot/sites/default/files/private/
  mkdir docroot/sites/default/files/translations/
  chmod 777 -R docroot/sites/default/files/
}

_run_npm_audit() {
  _info '### Npm audit'
  _info '# Install nvm'
  set +o errexit
  export NVM_DIR="$HOME/.nvm"
  [ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh" # This loads nvm
  _info '# Install node with nvm'
  set -o errexit
  nvm install --no-progress --latest-npm "$(cat .nvmrc)"
  _info '# Audit non-dev dependencies'
  npm install --production
  npm audit --production
}

_composer() {
  export COMPOSER_ALLOW_SUPERUSER
  composer --ansi --profile "$@"
}

_create_db_dump() {
  _drush sql:dump --extra-dump="--no-tablespaces" --result-file="/tmp/db.sql"
}

_copy_assets() {
  mkdir -p "$CI_ROOT_DIR"/project/docroot/sites/default/files/media-icons/generic
  cp -v "$CI_ROOT_DIR"/project/docroot/modules/contrib/media_entity_instagram/images/icons/instagram.png "$CI_ROOT_DIR"/project/docroot/sites/default/files/media-icons/generic/instagram.png
  cp -v "$CI_ROOT_DIR"/project/docroot/modules/contrib/media_entity_twitter/images/icons/twitter.png "$CI_ROOT_DIR"/project/docroot/sites/default/files/media-icons/generic/twitter.png
  cp -v "$CI_ROOT_DIR"/project/docroot/core/modules/media/images/icons/* "$CI_ROOT_DIR"/project/docroot/sites/default/files/media-icons/generic/
  cp -v "$CI_ROOT_DIR"/project/docroot/profiles/contrib/degov/modules/lightning_media/images/star.png "$CI_ROOT_DIR"/project/docroot/sites/default/files/
}

_disable_geocoder_presave() {
  # Geocoder ignores lat/long in Mediagenerator. (See function geocoder_field_entity_presave())
  _info "### Disable geocoder presave."
  _drush config:set geocoder.settings geocoder_presave_disabled ${1:-1}
}

_update_translations() {
  _info "### Update translations"
  _drush locale:check \
    && _drush locale:update
  _drush php-eval 'Drupal\degov\TranslationImporter::importForProfile()'
  _info "### Clear cache"
  _drush cr
}
