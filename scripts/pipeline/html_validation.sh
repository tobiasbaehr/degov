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
__SHARED_DIR__="$__STARTDIR__/html_validation_shared"
__TMP__="$__STARTDIR__/tmp"

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

_fetch_html_content() {
  local URLS=""
  URLS=$(jq --raw-output '[ .scenarios[] | select(has("skipValidation") | not) | .url ] | unique[]' ../../testing/backstopjs/backstop.json | tr '\n' ' ')
  rm  -rf "${__TMP__:?}"/* \
  && wget --hsts-file=/tmp/wget-hsts --no-verbose --no-cache --no-cookies --trust-server-names --directory-prefix "$__TMP__" $URLS
  local EXITCODE=$?
  if [[ "${EXITCODE:-}" -gt 0 ]] ;then
    _err "Could not fetch the HTML content. Is the host host.docker.internal running?"
  fi
  return $EXITCODE;
}

_run_validation() {
  if [[ -n "${CI:-}" ]];then
    docker run \
      -v "$__TMP__":/files \
      -v "$__SHARED_DIR__":/shared \
      --add-host host.docker.internal:$BITBUCKET_DOCKER_HOST_INTERNAL \
      validator/validator:latest /vnu-runtime-image/bin/vnu \
        --filterfile  /shared/message-filters.txt \
        --errors-only \
      /files
  else
    docker run \
      -v "$__TMP__":/files \
      -v "$__SHARED_DIR__":/shared \
      validator/validator:latest /vnu-runtime-image/bin/vnu \
        --filterfile  /shared/message-filters.txt \
        --errors-only \
      /files
  fi
  local EXITCODE=$?
  if [[ "${EXITCODE:-}" = 125 ]] ;then
    _err "Could not validate the HTML content. Docker is not running."
  fi
  if [[ "${EXITCODE:-}" = 1 ]] ;then
    _err "Found some validation errors."
  fi
  return $EXITCODE;
}

main() {
  if [[ -n "${CI:-}" ]];then
    echo "$BITBUCKET_DOCKER_HOST_INTERNAL host.docker.internal" >> /etc/hosts
  fi

  cd "$__STARTDIR__" \
  && _info "### Validating HTML5" \
  && _fetch_html_content \
  && _run_validation \
  && _info "No validation errors found"
}

main "$@"
