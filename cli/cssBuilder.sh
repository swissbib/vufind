#!/usr/bin/env bash

BASEDIR=$(dirname $0)
APP_DIR=$BASEDIR/..



while getopts p: OPTION
do
  case $OPTION in
    h) usage
        exit 9
        ;;
    p) USER_CACHEPATH=$OPTARG;;

    *) printf "unknown option -%c\n" $OPTION; usage; exit;;
  esac
done



if [ ! -z  ${USER_CACHEPATH} ]
then
    CACHEPATH=$USER_CACHEPATH

else
    CACHEPATH=${APP_DIR}/local/cache/cli

fi

export VUFIND_CACHE_DIR=$CACHEPATH

echo $VUFIND_CACHE_DIR
php ${APP_DIR}/util/cssBuilder.php




