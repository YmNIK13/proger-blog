FROM leadsdoit/lara-php:8.4-dev AS phpdev

RUN install-php-extensions \
    mysqli \
    pdo_mysql
