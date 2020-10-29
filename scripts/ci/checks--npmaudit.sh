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

main() {
  # shellcheck source=.
  source "$__DIR__/common_functions.sh"
  cd "$CI_ROOT_DIR/project"
  _info "### Run npm audit"
  local npm_audit_was_used=0
  if [[ -d "docroot/profiles/contrib/$CONTRIBNAME/themes" ]]; then
    cd "docroot/profiles/contrib/$CONTRIBNAME/themes/degov_theme"
    _info '# Audit non-dev dependencies'
    npm install --production
    npm audit --production
    npm_audit_was_used=1
  fi
  if [[ $npm_audit_was_used -eq 0 ]]; then
    _err "npm audit was not used. Did you move the Theme(s) ?"
    exit 1
  fi
}

main
