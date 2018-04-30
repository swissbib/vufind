#!/bin/bash
VUFIND_BASE=$(dirname $0)

BASEDIR=$(dirname $0)
INDEX="$BASEDIR/../public/index.php"
if [ -z "$LOCAL_DIR" ] # if $LOCAL_DIR empty or unset
then
    echo "You need to specify the LOCAL_DIR, i.e the vufind config to use : for example : bash -c 'export LOCAL_DIR=/usr/local/vufind/httpd/local/classic/XXXXXXX; ./cli/update-pura-user.sh'"
else
    export VUFIND_LOCAL_DIR=$LOCAL_DIR;
    export VUFIND_LOCAL_MODULES=Swissbib
    export VUFIND_LOCAL_DIR
    php $INDEX update-pura-user $@
fi



