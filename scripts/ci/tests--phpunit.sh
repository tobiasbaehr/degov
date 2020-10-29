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
  cd "$CI_ROOT_DIR/project"
  _info "### Running PHPUnit and KernelBase tests"
  (cd "docroot/profiles/contrib/$CONTRIBNAME" && phpunit --colors=auto --log-junit "$CI_ROOT_DIR/test-reports/phpunit/phpunit-junit.xml" --testdox)
}

main
