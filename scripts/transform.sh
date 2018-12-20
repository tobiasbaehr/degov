#!/usr/bin/env bash

# Usage example:
# var=`./transform.sh "--tag=$tag" [--increment]`

# set expected script args
tag=`echo ${*} | egrep -o '(--tag=)(-?[a-zA-Z0-9\-\.]+)+' | awk -F'=' '{print $2}'`

# parameter is required
if [ -z ${tag} ]; then
  echo "parameter --tag=<tag> is required";
  exit -1;
fi

if [ $2 ]; then
    if [ $2 == "--increment" ]; then
        regex="([0-9]+)\.([0-9]+).([0-9]+)"
        if [[ ${tag} =~ $regex ]]; then
            major=${BASH_REMATCH[1]}
            minor=${BASH_REMATCH[2]}
            patch=${BASH_REMATCH[3]}
            echo "${major}.${minor}.$(($patch + 1))"
        else
            echo "No valid tag"
            exit -1;
        fi
        exit 0;
    fi
fi

regex="([0-9]+)\.([0-9]+).([0-9]+)-?([a-z0-9]+)?"
if [[ $tag =~ $regex ]]; then
    major=${BASH_REMATCH[1]}
    minor=${BASH_REMATCH[2]}
    patch=${BASH_REMATCH[3]}
    merged=""
    suffix=""

    if [ $minor == "0" ]; then
        if [ $patch -lt 10 ]; then
            $patch="0${patch}"
        fi
        merged=$patch
    else
        merged="${minor}${patch}"
    fi

    if [ ! -z ${BASH_REMATCH[4]} ]; then
        suffix=-${BASH_REMATCH[4]}
    fi

    final="8.x-${major}.${merged}${suffix}"
    echo ${final}
fi