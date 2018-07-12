#!/usr/bin/env bash
set -e
echo "    StrictHostKeyChecking no" >> /etc/ssh/ssh_config
git clone git@bitbucket.org:/publicplan/degov_project.git
cd degov_project
composer update degov/degov
git add composer.lock
git commit -m "Updating deGov dependencies automatically"
git push
TAG=./docroot/profiles/degov/scripts/transform.sh --tag=$(git describe --tags --abbrev=0) --increment
git tag ${TAG}
git push origin ${TAG}