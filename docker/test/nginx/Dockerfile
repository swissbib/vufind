#
# Image for the nginx server
#
FROM nginx:alpine

ENV VUFIND_CACHE_DIR=/var/cache/vufind \
  VUFIND_APPLICATION_PATH=/usr/local/vufind

RUN mkdir -p ${VUFIND_CACHE_DIR} ${VUFIND_APPLICATION_PATH} ${VUFIND_CACHE_DIR}

COPY ./docker/conf/nginx.conf /etc/nginx/conf.d/default.conf

COPY ./local ${VUFIND_APPLICATION_PATH}/local
COPY ./themes ${VUFIND_APPLICATION_PATH}/themes
