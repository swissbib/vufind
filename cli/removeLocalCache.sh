#!/bin/bash
#
# Removal of local caches (except hierarchy cache)

VUFIND_BASE=/usr/local/vufind/httpd2
#VUFIND_CACHE=$VUFIND_BASE/local/cache
VUFIND_DEPLOY_LOG=$VUFIND_BASE/log
TIMESTAMP=`date +%Y%m%d%H%M%S`  # seconds
LOGFILE=$VUFIND_DEPLOY_LOG/remove.local.cache.$TIMESTAMP.log

if [ "$UID"  -eq 0 ]; then

    declare -a hosts=("local/classic/local/cache"
                     "local/baselbern/local/cache"
                     "local/jus/local/cache"
                     "local/classic/test/cache"
                     "local/baselbern/test/cache"
                     "local/jus/justest/cache"
                     "local/classic/develop/cache"
                     "local/classic/shibdev/cache"
                     "local/baselbern/devbabe/cache"
                     "local/jus/jusdev/cache"
                     "local/classic/productive/cache"
                     "local/baselbern/productive/cache"
                     "local/jus/productive/cache"
                     "local/classic/c2sbmanually/cache"
                     )

    for cacheDir in "${hosts[@]}"
    do
        VUFIND_CACHE=$VUFIND_BASE/${cacheDir}
        echo "Trying to remove local cache: ${VUFIND_CACHE}"
        # no removal of hierarchy cache
        rm -rf $VUFIND_CACHE/configs/*
        rm -rf $VUFIND_CACHE/searchspecs/*
        rm -rf $VUFIND_CACHE/objects/*
        rm -rf $VUFIND_CACHE/languages/*
    done

    echo "now restart apache ..."

    service apache2 restart

else
        echo "You have to be root to start this script!"
        exit 1
fi