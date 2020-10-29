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
  if [[ ! -d $PROFILE_DIR ]]; then
    # Restore the data which was deleted in composer_setup.sh and is not part of the artifact.
    if [[ ! -d "$CI_ROOT_DIR/project/docroot/profiles/contrib" ]]; then
      mkdir "$CI_ROOT_DIR/project/docroot/profiles/contrib"
    fi
    if [[ ! -d "$CI_ROOT_DIR/testing/lfs_data" ]]; then
      mkdir "$CI_ROOT_DIR/testing/lfs_data"
    fi
    mv -v "$CI_ROOT_DIR/project/$CONTRIBNAME-stable-$DB_DUMP_VERSION.sql.gz" "$CI_ROOT_DIR/testing/lfs_data/$CONTRIBNAME-stable-$DB_DUMP_VERSION.sql.gz"
    (cd "$CI_ROOT_DIR" && rsync -azv --exclude="project/" . "$PROFILE_DIR")
  fi
}

main "$@"
