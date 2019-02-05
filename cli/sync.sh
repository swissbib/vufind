#!/bin/bash
#
# sync with libadmin and clear cache

VUFIND_BASE=/usr/local/vufind/httpd

if [ "$UID"  -ne 0 ]; then
    echo "You have to be root to use the script because cache will be cleared"
    exit 1
fi

BASEDIR=$(dirname $0)
INDEX="$BASEDIR/../public/index.php"
if [ -z "$LOCAL_DIR" ]; # if $LOCAL_DIR empty or unset, use default localdir
   then export VUFIND_LOCAL_DIR=${BASEDIR}/../local;
   else export VUFIND_LOCAL_DIR=$LOCAL_DIR;
fi

export VUFIND_CACHE=$VUFIND_LOCAL_DIR/cache
export VUFIND_LOCAL_MODULES=Swissbib

php $INDEX libadmin syncGeoJson $@
ln -s $VUFIND_BASE/data/cache/geojson.json $VUFIND_BASE/public/geojson.json

su -c "php $INDEX libadmin sync $@" vfsb

#please do not delete a directory with options -rf as root based on a relative directory! GH
echo "Trying to remove local cache"
# no removal of hierarchy cache
rm -rf $VUFIND_CACHE/searchspecs/*
rm -rf $VUFIND_CACHE/objects/*
rm -rf $VUFIND_CACHE/languages/*

service apache2 restart
