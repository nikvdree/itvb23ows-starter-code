FROM php:8.0-apache

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-install session && docker-php-ext-enable session

COPY . /var/www/html

WORKDIR /var/www/html

CMD ["php", "-S", "0.0.0.0:8000"]
