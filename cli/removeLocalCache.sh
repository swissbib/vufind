#!/bin/bash
#
# Removal of local caches (except hierarchy cache)

VUFIND_BASE=/usr/local/vufind/httpd
VUFIND_CACHE=$VUFIND_BASE/local/cache
VUFIND_DEPLOY_LOG=$VUFIND_BASE/log
TIMESTAMP=`date +%Y%m%d%H%M%S`  # seconds
LOGFILE=$VUFIND_DEPLOY_LOG/remove.local.cache.$TIMESTAMP.log

if [ "$UID"  -eq 0 ]; then

    echo "Tryinig to remove local cache"
    # no removal of hierarchy cache
    rm -rf $VUFIND_CACHE/searchspecs/*
    rm -rf $VUFIND_CACHE/objects/*
    rm -rf $VUFIND_CACHE/languages/*

    echo "now restart apache ..."

    service apache2 restart

else
        echo "You have to be root to start this script!"
        exit 1
fi