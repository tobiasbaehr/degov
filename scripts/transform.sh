#!/usr/bin/env bash

# Usage example:
# var=`./transform.sh "--tag=$tag"`

# set expected script args
tag=`echo ${*} | egrep -o '(--tag=)(-?[a-zA-Z0-9\-\.]+)+' | awk -F'=' '{print $2}'`
# parameter is required
if [ -z $tag ]; then
  echo "parameter --tag=<tag> is required";
  exit -1;
fi

regex="([0-9]+)\.([0-9]+\.[0-9]+)-?([a-z0-9]+)?"
if [[ $tag =~ $regex ]]; then
    major=${BASH_REMATCH[1]}
    minor=`sed -e "s/\.//g" <<< "${BASH_REMATCH[2]}"`

    if [ $minor == "00" ]; then
        minor="0"
    fi

    if [ ! -z ${BASH_REMATCH[3]} ]; then
        suffix=-${BASH_REMATCH[3]}
    fi

    # fix starting null in version tag
    if [[ $minor == "0[0-9]*" ]]; then
        minor=${minor:1}
    fi

    final="8.x-${major}.${minor}${suffix}"
    echo ${final}
fi