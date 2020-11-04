#!/bin/bash
#
# sync with libadmin and clear cache

VUFIND_BASE=/usr/local/vufind/httpd


BASEDIR=$(dirname $0)
INDEX="$BASEDIR/../public/index.php"
if [ -z "$LOCAL_DIR" ]; # if $LOCAL_DIR empty or unset, use default localdir
   then export VUFIND_LOCAL_DIR=${BASEDIR}/../local;
   else export VUFIND_LOCAL_DIR=$LOCAL_DIR;
fi

export VUFIND_CACHE=$VUFIND_LOCAL_DIR/cache
export VUFIND_LOCAL_MODULES=Swissbib
export VUFIND_LOCAL_DIR
#export APPLICATION_ENV=development

php $INDEX libadmin/syncMapPortal $@

#please do not delete a directory with options -rf as root based on a relative directory! GH
