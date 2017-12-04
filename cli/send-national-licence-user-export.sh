#!/bin/bash
VUFIND_BASE=$(dirname $0)
VUFIND_CACHE=$VUFIND_BASE/local/cache

BASEDIR=$(dirname $0)
INDEX="$BASEDIR/../public/index.php"
if [ -z "$LOCAL_DIR" ]; # if $LOCAL_DIR empty or unset, use default localdir
   then export VUFIND_LOCAL_DIR=${BASEDIR}/../local/classic/productive;
   else export VUFIND_LOCAL_DIR=$LOCAL_DIR;
fi

export VUFIND_LOCAL_MODULES=Swissbib
export VUFIND_LOCAL_DIR

php $INDEX send-national-licence-users-export $@

rm -rf $VUFIND_CACHE/searchspecs/*
rm -rf $VUFIND_CACHE/objects/*
rm -rf $VUFIND_CACHE/languages/*
