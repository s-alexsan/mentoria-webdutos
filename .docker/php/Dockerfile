FROM php:8.2-apache
RUN apt-get update && apt-get install -y \
    libfbclient2 firebird-dev \
    && docker-php-ext-install pdo_firebird
RUN a2enmod rewrite