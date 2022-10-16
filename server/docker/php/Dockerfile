ARG PHP_SOURCE_IMAGE=php:8.1-fpm-alpine

FROM $PHP_SOURCE_IMAGE as stateful

ARG ENV=dev
ARG XDEBUG_ENABLED=0

RUN set xe && \
    cd /tmp/ && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "echo hash_file('sha384', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php --install-dir=/usr/local/bin/ --filename=composer && \
    php -r "unlink('composer-setup.php');"

RUN set xe && \
    cd /tmp/ && \
    apk add --no-cache --update mysql-dev && \
    docker-php-ext-install -j5 pdo pdo_mysql

RUN if [ "${XDEBUG_ENABLED}" = "1" ] ;then set -xe && \
    cd /tmp/ && \
    apk add --no-cache --update --virtual .phpize-deps $PHPIZE_DEPS && \
    # Install xdebug
    pecl install xdebug && \
    # Enable PHP extensions
    docker-php-ext-enable xdebug && \
    apk del .phpize-deps \
;fi

COPY ./docker/php/php.ini-development "$PHP_INI_DIR/php.ini-development"
COPY ./docker/php/php.ini-production "$PHP_INI_DIR/php.ini-production"

RUN if [ "${ENV}" = "prod" ] ;then set -xe && \
    mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
;fi

RUN if [ "${ENV}" = "dev" ] ;then set -xe && \
    mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" \
;fi

# @link https://github.com/renatomefi/php-fpm-healthcheck
RUN apk add --no-cache --update fcgi
# Enable php fpm status page
RUN set -xe && echo "pm.status_path = /status" >> /usr/local/etc/php-fpm.d/zz-docker.conf
RUN wget -O /usr/local/bin/php-fpm-healthcheck \
    https://raw.githubusercontent.com/renatomefi/php-fpm-healthcheck/master/php-fpm-healthcheck \
    && chmod +x /usr/local/bin/php-fpm-healthcheck
RUN echo "53bc616c4a30f029b98bff48fdeb0c4da252cb11e4f86656a8222a67dc4e5009  /usr/local/bin/php-fpm-healthcheck" | sha256sum -c

FROM stateful AS stateless

ARG ENV=dev
ENV COMPOSER_HOME=/app/.composer.cache

COPY ./ /app

WORKDIR /app

ENV COMPOSER_ALLOW_SUPERUSER 1

RUN set -xe && \
    echo APP_ENV="$ENV" > .env.local && \
    echo DATABASE_URL="" >> .env.local && \
    if [ "${ENV}" = "prod" ] ;then \
    composer install --optimize-autoloader --no-dev \
    ;else \
    composer install --optimize-autoloader \
    ;fi  && \
    composer dump-autoload --classmap-authoritative --optimize && \
    composer dump-env "$ENV" && \
    php bin/console cache:warmup && \
    rm -rf $COMPOSER_HOME