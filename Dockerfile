FROM php:7.3-fpm

RUN apt-get update && apt-get -y install git

RUN mkdir /data
WORKDIR /data

RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/local/bin --filename=composer

ENV XDEBUG_VERSION 2.7.1
RUN pecl install channel://pecl.php.net/xdebug-${XDEBUG_VERSION}
COPY docker/xdebug.ini $PHP_INI_DIR/conf.d/xdebug.ini

CMD ["php-fpm"]
