FROM php:7.4-fpm-alpine

# Install DB driver for PHP => pdo pdo_mysql
RUN set -xe \
    && apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS \
    && apk add git \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -rf /usr/share/php \
    && rm -rf /tmp/* \
    && apk del  .phpize-deps

# Override php.ini settings
COPY ./docker/symfony/app_custom.ini /usr/local/etc/php/conf.d/app_custom.ini

WORKDIR /var/www/html

COPY . /var/www/html

# Prepare script copy and execution permission
COPY ./docker/symfony/prepare.sh .
RUN chmod a+x ./prepare.sh

# Install composer
RUN curl --show-error --silent https://getcomposer.org/installer | php \
    && php composer.phar install -v