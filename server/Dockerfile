FROM webdevops/php-nginx:8.1
COPY ./ /app
RUN composer install -d /app

# install xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo "xdebug.client_host = host.docker.internal" >>  \
    /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /app

LABEL org.opencontainers.image.source=https://github.com/Dudoserovich/EduGameTheory