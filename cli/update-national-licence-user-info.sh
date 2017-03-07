#!/bin/bash
VUFIND_BASE=$(dirname $0)
VUFIND_CACHE=$VUFIND_BASE/local/cache

BASEDIR=$(dirname $0)
INDEX="$BASEDIR/../public/index.php"
VUFIND_LOCAL_DIR="$BASEDIR/../local"

export VUFIND_LOCAL_MODULES=Swissbib
export VUFIND_LOCAL_DIR
export APPLICATION_ENV=development
#Check the environmnet
if [ "$APPLICATION_ENV" == "development" ]
then
    SWITCH_API_USER="${SWITCH_API_USER:=natlic}"
    SWITCH_API_PASSW="${SWITCH_API_PASSW:=Amg6vZXo}"
else
    #Set these environment variable for production to connect to the SWITCH API
    SWITCH_API_USER="${SWITCH_API_USER:=}"
    SWITCH_API_PASSW="${SWITCH_API_PASSW:=}"
fi
export SWITCH_API_USER;
export SWITCH_API_PASSW;

#Check that the SWITCH API has been correctly configured.
: "${SWITCH_API_USER:? The environment variable SWITCH_API_USER have to be set}"
: "${SWITCH_API_USER:? The environment variable SWITCH_API_PASSW have to be set}"

php $INDEX update-national-licence-user-info $@

rm -rf $VUFIND_CACHE/searchspecs/*
rm -rf $VUFIND_CACHE/objects/*
rm -rf $VUFIND_CACHE/languages/*
