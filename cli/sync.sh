#!/bin/bash
#
# sync with libadmin and clear cache

VUFIND_BASE=/usr/local/vufind/httpd
VUFIND_CACHE=$VUFIND_BASE/local/cache

if [ "$UID"  -ne 0 ]; then
    echo "You have to be root to use the script because cache will be cleared"
    exit 1
fi

BASEDIR=$(dirname $0)
INDEX="$BASEDIR/../public/index.php"
VUFIND_LOCAL_DIR="$BASEDIR/../local"

export VUFIND_LOCAL_MODULES=Swissbib
export VUFIND_LOCAL_DIR
#export APPLICATION_ENV=development

su -c "php $INDEX libadmin sync $@" vfsb

#please do not delete a directory with options -rf as root based on a relative directory! GH
echo "Tryinig to remove local cache"
# no removal of hierarchy cache
rm -rf $VUFIND_CACHE/searchspecs/*
rm -rf $VUFIND_CACHE/objects/*
rm -rf $VUFIND_CACHE/languages/*

service apache2 restart