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

_info() {
  local color_info='\x1b[96m'
  local color_reset='\x1b[0m'
  echo -e "$(printf '%s%s%s\n' "$color_info" "$@" "$color_reset")"
}

main() {
  mkdir -p "$CI_ROOT_DIR/test-reports/phpcs"
  cd "$CI_ROOT_DIR/project"
  _info "### Check php compatibility"
  phpcs -p -s --standard=phpcompatibility.xml
  _info "### Check coding standards"
  phpcs --report=junit --report-file="$CI_ROOT_DIR/test-reports/phpcs/phpcs-junit.xml"
}

main
