#!/usr/bin/env bash

SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
#echo $SCRIPT_DIR

[ -d createDocker ] &&  rm -r createDocker
mkdir createDocker

cp -rp cli  config languages local log module packages public themes util .eslintrc.js createDocker
cp -rp build.xml composer.json composer.lock Gruntfile.js createDocker
cp -rp package.json tsconfig.json webpack.config.js  createDocker
cp -p Dockerfile_ubuntu createDocker/Dockerfile
#cp -p Dockerfile createDocker

cd createDocker ||  exit

docker build -t swissbib .

cd $SCRIPT_DIR && rm -r createDocker
