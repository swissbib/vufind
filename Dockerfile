FROM php:7.2-apache

LABEL maintainer="Guenter Hipler"

COPY --chown=www-data:www-data . /var/www/html

RUN curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer


#checking for cURL 7.10.5 or greater... configure: error: cURL version 7.10.5 or later is required to compile php with cURL support
#curl should be available
#bz2  (bekommt einen Konfigurationsfehler)
#cli common dev fpm mysql  xdebug BZip2 (ich bekomme einen Konfigurationsfehler)
RUN docker-php-ext-install pdo_mysql bcmath \
   dba \
   gd intl json mbstring opcache \
   readline soap xml zip

WORKDIR /var/www/html

RUN composer install
