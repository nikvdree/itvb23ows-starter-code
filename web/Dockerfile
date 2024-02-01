FROM php:8.0-apache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apt-get update \
  && apt-get install zip -y \
  && apt-get clean

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-install session && docker-php-ext-enable session

COPY . /var/www/html

WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install

CMD ["php", "-S", "0.0.0.0:8000"]
