#!/usr/bin/env bash

set -o nounset
set -o pipefail
set -o errexit

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
  ln -sf "$CI_ROOT_DIR/php_error.log" /tmp/php_error.log
  if [[ -n ${BITBUCKET_DOCKER_HOST_INTERNAL:-} ]]; then
    echo "$BITBUCKET_DOCKER_HOST_INTERNAL host.docker.internal" >> /etc/hosts
  fi
  if [[ ! -d $CI_ROOT_DIR ]]; then
    # Restore the data which was deleted in composer_setup.sh and is not part of the artifact.
    mkdir "$CI_ROOT_DIR/project/docroot/profiles/contrib"
    mkdir "$CI_ROOT_DIR/testing/lfs_data"
    mv -v "$CI_ROOT_DIR/project/$CONTRIBNAME-stable-$DB_DUMP_VERSION.sql.gz" "$CI_ROOT_DIR/testing/lfs_data/$CONTRIBNAME-stable-$DB_DUMP_VERSION.sql.gz"
    (cd "$CI_ROOT_DIR" && rsync -az --exclude="project/" . "$PROFILE_DIR")
  fi
}

main "$@"
