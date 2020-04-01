#!/usr/bin/env bash

set -o nounset
set -o pipefail
set -o errexit

if [[ -n "${DEBUG:-}" ]];then
  set -o xtrace
fi

# shellcheck disable=SC2164
__DIR__="$(cd "$(dirname "${0}")"; pwd)"

main() {
  # shellcheck source=.
  source "$__DIR__/../.env"
  bash "$__DIR__/../default_setup_ci.sh"
  # shellcheck source=.
  source "$__DIR__/common_functions.sh"

  cd project
  ## TODO needs work, see https://publicplan.atlassian.net/browse/DEGOV-659
  ## echo "### Checking code standards"
  ## phpstan analyse docroot/profiles/contrib/degov -c docroot/profiles/contrib/degov/phpstan.neon --level=1 || true
  _info "### Running PHPUnit and KernelBase tests"
  (cd "docroot/profiles/contrib/$CONTRIBNAME" && phpunit --colors=auto --log-junit $BITBUCKET_CLONE_DIR/test-reports/junit.xml --testdox)
  _info "### Checking coding standards"
  phpcs --report=junit --report-file=$BITBUCKET_CLONE_DIR/test-reports/junit.xml
  _info "### Run npm audit"
  local npm_audit_was_used=0;
  if [[ -d "docroot/profiles/contrib/$CONTRIBNAME/themes" ]];then
    (cd "docroot/profiles/contrib/$CONTRIBNAME/themes/degov_theme" && _run_npm_audit)
    npm_audit_was_used=1
  fi
  if [[ -d "docroot/themes/nrw/nrw_base_theme" ]];then
    (cd "docroot/themes/nrw/nrw_base_theme" && _run_npm_audit)
    npm_audit_was_used=1
  fi
  if [[ "$npm_audit_was_used" -eq 0 ]];then
    _err "npm audit was not used. Did you move the Theme(s) ?"
    exit 1
  fi
}

main "$@"
