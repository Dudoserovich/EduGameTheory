FROM webdevops/php-nginx:8.1

ARG ENV=dev

# set timezone
RUN echo date.timezone = ASIA >> /opt/docker/etc/php/php.ini
# install xdebug
# disable xdebug for prod (multi-stage build)
RUN if [ "${ENV}" = "dev" ] ;then \
    pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host = host.docker.internal" >>  \
    /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.discover_client_host=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
;fi

# Установка сервисов для оптимизации изображений
RUN apt-get update && apt-get install -y \
    jpegoptim \
    optipng \
    pngquant \
#    gifsicle \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

LABEL org.opencontainers.image.source=https://github.com/Dudoserovich/EduGameTheory

# Установка зависимостей composer
ENV COMPOSER_ALLOW_SUPERUSER 1
COPY ./composer.json ./composer.lock ./.env /app/

RUN set -xe && \
    echo APP_ENV="$ENV" > .env.local && \
    if [ "${ENV}" = "prod" ] ;then \
    composer install --optimize-autoloader --no-dev \
    ;else \
    composer install --optimize-autoloader \
    ;fi  && \
    composer dump-autoload --classmap-authoritative --optimize && \
    composer dump-env "$ENV"
COPY ./ /app
