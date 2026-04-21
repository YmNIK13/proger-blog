FROM leadsdoit/lara-php:8.4-dev AS development

RUN install-php-extensions \
    gd \
    mysqli \
    pdo_mysql


FROM node:24-alpine AS assets-builder

WORKDIR /build

COPY web/app/themes/progger-blog/package*.json ./
RUN npm ci

COPY web/app/themes/progger-blog ./
RUN npm run build


FROM leadsdoit/lara-php:8.4 AS production

WORKDIR /app

RUN install-php-extensions \
    gd \
    mysqli \
    pdo_mysql

COPY composer.json composer.lock ./
COPY config ./config
COPY web/index.php ./web/index.php
COPY web/app ./web/app
COPY web/wp-config.php ./web/wp-config.php
COPY wp-cli.yml ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader

COPY --from=assets-builder /build/public /app/web/app/themes/progger-blog/public

RUN chown -R www-data:www-data /app


FROM nginx:alpine AS nginx-production

WORKDIR /app

COPY docker/nginx.prod.conf /etc/nginx/conf.d/default.conf
COPY --from=production /app/web /app/web
