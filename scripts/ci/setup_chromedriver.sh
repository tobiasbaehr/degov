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
  local version="${1:-86.0.4240.22}"
  local targetDir=${2:-/tmp}
  if [[ ! -f "$targetDir/chromedriver" ]]; then
    wget -O /tmp/chromedriver_linux64.zip "https://chromedriver.storage.googleapis.com/$version/chromedriver_linux64.zip" \
      && unzip /tmp/chromedriver_linux64.zip -O "$targetDir/chromedriver" \
      && chmod +x "$targetDir/chromedriver" \
      && _info "Downloaded chromedriver in $version to $targetDir/chromedriver"
    exit 0
  fi
  _info "[SKIP]Already downloaded"
}

main "$@"
