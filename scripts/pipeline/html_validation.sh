#!/usr/bin/env bash

# Check unset variables
set -o nounset
set -o pipefail

if [[ -n "${DEBUG:-}" ]];then
  set -o xtrace
fi

# shellcheck disable=SC2164
__DIR__="$(cd "$(dirname "${0}")"; pwd)"
__STARTDIR__=${__STARTDIR__:-$__DIR__}
__TMP__="$__STARTDIR__/tmp"

_info() {
  local color_info="\\x1b[32m"
  local color_reset="\\x1b[0m"
  echo -e "$(printf '%s%s%s\n' "$color_info" "$@" "$color_reset")"
}

_fetch_html_content() {
  local URLS=""
  URLS=$(jq --raw-output '[.scenarios[] | .url ] | unique[]' ../../testing/backstopjs/backstop.json | tr '\n' ' ')
  wget --hsts-file=/tmp/wget-hsts --quiet --trust-server-names --continue --directory-prefix "$__TMP__" $URLS
}

_run_validation_extended() {
  URLS=$(jq --raw-output '[.scenarios[] | .url ] | unique[]' ../../testing/backstopjs/backstop.json | tr '\n' ' ')
  mkdir -p ../../test-reports
  local REPORTS="../../test-reports"
  for url in $URLS;do
    local url_encoded=""
    url_encoded=$(php -r "echo urlencode('$url');")
    curl -s -L -H "Content-Type: text/html; charset=utf-8" "localhost:8888?parser=html&level=error&out=html&checkerrorpages=false&doc=$url_encoded" > "$REPORTS/$url_encoded".html
    html2text -nobs -utf8 -style pretty "$REPORTS/$url_encoded".html
  done
}

_run_validation() {
  local rc=0
  if [[ -n "${CI:-}" ]];then
    docker run -v "$__TMP__":/files --add-host host.docker.internal:$BITBUCKET_DOCKER_HOST_INTERNAL validator/validator:latest /vnu-runtime-image/bin/vnu --errors-only /files
    rc=$?
  else
    docker run -v "$__TMP__":/files validator/validator:latest /vnu-runtime-image/bin/vnu --errors-only /files
  fi
  if [[ -n "${ERROR:-}" ]] && [[ "${ERROR:-}" -gt 0 ]] ;then
    return $rc
  fi
}

main() {
  if [[ -n "${CI:-}" ]];then
    echo "$BITBUCKET_DOCKER_HOST_INTERNAL host.docker.internal" >> /etc/hosts
  fi

  cd "$__STARTDIR__" \
  && _info "### Validating HTML5" \
  && _fetch_html_content \
  && _run_validation
}

main "$@"
