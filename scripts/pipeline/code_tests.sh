#!/usr/bin/env bash

# Unset variables
set -o nounset
set -o pipefail
# hard fail
set -o errexit

ln -sf $BITBUCKET_CLONE_DIR/php_error.log /tmp/php_error.log

_info() {
  local color_info="\\x1b[32m"
  local color_reset="\\x1b[0m"
  echo -e "$(printf '%s%s%s\n' "$color_info" "$@" "$color_reset")"
}

_err() {
  local color_error="\\x1b[31m"
  local color_reset="\\x1b[0m"
  echo -e "$(printf '%s%s%s\n' "$color_error" "$@" "$color_reset")" 1>&2
}

_load_environment_variables() {
  set -o allexport;
  envFilePath=$BITBUCKET_CLONE_DIR/scripts/pipeline/.env
  if [ -f $envFilePath ]
  then
    _info "Parsing .env file from path: $envFilePath"
    source $envFilePath
  else
    _err "Could not parse .env file in path: $envFilePath"
    EXIT_CODE=$?
    exit $EXIT_CODE
  fi
  set +o allexport
}
_load_environment_variables

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

_info "### Setting up project folder"
_composer --no-progress create-project degov/degov-project:dev-$PROJECT_BRANCH --no-install
cd degov-project
rm composer.lock
_info "### Install profile"
_composer require --no-progress "degov/degov:dev-$BITBUCKET_BRANCH#$BITBUCKET_COMMIT" --update-with-all-dependencies

PATH="$(pwd)/bin/:$PATH"
export PATH

_composer dump-autoload
# TODO needs work, see https://publicplan.atlassian.net/browse/DEGOV-659
# echo "### Checking code standards"
# phpstan analyse docroot/profiles/contrib/degov -c docroot/profiles/contrib/degov/phpstan.neon --level=1 || true
_info "### Running PHPUnit and KernelBase tests"
(cd docroot/profiles/contrib/degov && phpunit --colors=auto --log-junit $BITBUCKET_CLONE_DIR/test-reports/junit.xml --testdox)

_info "### Checking coding standards"
bin/phpcs --report-full=$BITBUCKET_CLONE_DIR/coding-standards.txt || true
PHPCS=$(grep "ERROR" $BITBUCKET_CLONE_DIR/coding-standards.txt | wc -l || true)
if [[ $PHPCS -ne 0 ]]; then
  exit 1
fi
