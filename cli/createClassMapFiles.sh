#!/bin/sh


BASEDIR=$(dirname $0)
APP_DIR=$BASEDIR/..


CLASSMAP_GENERATOR=${APP_DIR}/vendor/zendframework/zendframework/bin/classmap_generator.php

php ${CLASSMAP_GENERATOR} --library ${APP_DIR}/module/Swissbib/src/Swissbib --overwrite --output ${APP_DIR}/module/Swissbib/src/autoload_classmap.php
php ${CLASSMAP_GENERATOR} --library ${APP_DIR}/module/Jusbib/src/Jusbib --overwrite --output ${APP_DIR}/module/Jusbib/src/autoload_classmap.php
php ${CLASSMAP_GENERATOR} --library ${APP_DIR}/module/QRCode/src/QRCode --overwrite --output ${APP_DIR}/module/QRCode/src/autoload_classmap.php
php ${CLASSMAP_GENERATOR} --library ${APP_DIR}/module/Libadmin/src/Libadmin --overwrite --output ${APP_DIR}/module/Libadmin/src/autoload_classmap.php
php ${CLASSMAP_GENERATOR} --library ${APP_DIR}/module/VuFind/src/VuFind --overwrite --output ${APP_DIR}/module/VuFind/src/autoload_classmap.php
php ${CLASSMAP_GENERATOR} --library ${APP_DIR}/module/VuFindSearch/src/VuFindSearch --overwrite --output ${APP_DIR}/module/VuFindSearch/src/autoload_classmap.php
php ${CLASSMAP_GENERATOR} --library ${APP_DIR}/module/VuFindTheme/src/VuFindTheme --overwrite --output ${APP_DIR}/module/VuFindTheme/src/autoload_classmap.php
php ${CLASSMAP_GENERATOR} --library ${APP_DIR}/vendor/zendframework/zendframework/library/Zend --overwrite --output ${APP_DIR}/vendor/zendframework/zendframework/library/Zend/autoload_classmap.php


