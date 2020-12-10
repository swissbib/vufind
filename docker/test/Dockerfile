FROM node:10.18.1-alpine3.9-om




FROM php:7.4-fpm-alpine
VOLUME [ "/usr/local/vufind" ]
ARG vufind_env=development
ENV VUFIND_CACHE_DIR=/var/cache/vufind \
  VUFIND_APPLICATION_PATH=/usr/local/vufind \
  VUFIND_LOCAL_DIR=/usr/local/vufind/local \
  VUFIND_ENV=$vufind_env

RUN mkdir -p ${VUFIND_CACHE_DIR} \
  && chown www-data:www-data ${VUFIND_APPLICATION_PATH} ${VUFIND_CACHE_DIR} -R \
  && apk add --no-cache --update --virtual .build-deps icu-dev freetype-dev libjpeg-turbo-dev libpng-dev libxslt-dev zlib-dev bzip2-dev libzip-dev oniguruma-dev autoconf \
  && apk add --no-cache --update libpng libjpeg-turbo freetype icu-libs libxslt libzip \
  && apk add --no-cache --update libcurl curl-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) mbstring intl json mysqli pdo_mysql pdo gd xsl xml curl zip bz2  \
  && apk del .build-deps

COPY ./local ${VUFIND_APPLICATION_PATH}/local
COPY ./config ${VUFIND_APPLICATION_PATH}/config
COPY ./languages ${VUFIND_APPLICATION_PATH}/languages
COPY ./module ${VUFIND_APPLICATION_PATH}/module
COPY ./public ${VUFIND_APPLICATION_PATH}/public
COPY ./themes ${VUFIND_APPLICATION_PATH}/themes
COPY ./util ${VUFIND_APPLICATION_PATH}/util
COPY ./node_modules ${VUFIND_APPLICATION_PATH}/node_modules
COPY ./vendor ${VUFIND_APPLICATION_PATH}/vendor
# Required by Swissbib Logger. Rather log to stdout!
COPY --chown=www-data:www-data ./log ${VUFIND_APPLICATION_PATH}/log

WORKDIR ${VUFIND_APPLICATION_PATH}

CMD [ "php-fpm" ]
