#!/bin/bash

#CACHE_DIR=/usr/local/vufind/httpd/local/cache


#if [ "$UID"  -ne 0 ]; then
#        echo "you have to be root to use the git update script because cache will be cleared"
#        exit 1
#fi


BASEDIR=$(dirname $0)
INDEX="$BASEDIR/../public/index.php"

if [ -z "$LOCAL_DIR" ]; # if $LOCAL_DIR empty or unset, use default localdir
   then export VUFIND_LOCAL_DIR=${BASEDIR}/../local;
   else export VUFIND_LOCAL_DIR=$LOCAL_DIR;
fi

export VUFIND_LOCAL_MODULES=Swissbib
export VUFIND_LOCAL_DIR
#export APPLICATION_ENV=development

php $INDEX tab40import $@

#su -c "php $INDEX tab40import $@" vfsb

#please do not delete a directory with options -rf as root based on a relative directory! GH
#rm -rf $CACHE_DIR/*