FROM leadsdoit/lara-php:8.4-dev AS development

WORKDIR /app

RUN install-php-extensions \
    gd \
    mysqli \
    pdo_mysql


FROM node:24-alpine AS assets-builder

WORKDIR /app/web/app/themes/proger-blog

COPY web/app/themes/proger-blog/package*.json web/app/themes/proger-blog/.npmrc ./
RUN npm ci

COPY web/app/themes/proger-blog ./
RUN npm run build


FROM leadsdoit/lara-php:8.4 AS production

WORKDIR /app

ARG WWWUSER=1000
ARG WWWGROUP=1000

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN install-php-extensions \
    gd \
    mysqli \
    pdo_mysql

RUN set -eux; \
    current_group="$(id -g www-data)"; \
    current_user="$(id -u www-data)"; \
    if [ "$current_group" != "$WWWGROUP" ]; then \
        groupmod -o -g "$WWWGROUP" www-data; \
    fi; \
    if [ "$current_user" != "$WWWUSER" ]; then \
        usermod -o -u "$WWWUSER" www-data; \
    fi

COPY composer.json composer.lock ./
COPY config ./config
COPY web/index.php ./web/index.php
COPY web/wp-config.php ./web/wp-config.php
COPY wp-cli.yml ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader

COPY web/app ./web/app
COPY --from=assets-builder /app/web/app/themes/proger-blog/build ./web/app/themes/proger-blog/build

RUN mkdir -p /app/web/app/uploads \
    && chown -R www-data:www-data /app


FROM nginx:alpine AS nginx-production

WORKDIR /app

COPY docker/nginx.prod.conf /etc/nginx/conf.d/default.conf
COPY --from=production /app/web /app/web
