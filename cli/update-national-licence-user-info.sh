#!/bin/bash
VUFIND_BASE=$(dirname $0)
VUFIND_CACHE=$VUFIND_BASE/local/cache

BASEDIR=$(dirname $0)
INDEX="$BASEDIR/../public/index.php"
VUFIND_LOCAL_DIR="$BASEDIR/../local"

export VUFIND_LOCAL_MODULES=Swissbib
export VUFIND_LOCAL_DIR


php $INDEX update-national-licence-user-info $@


