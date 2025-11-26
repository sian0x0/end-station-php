FROM php:8.3-apache
LABEL authors="sian0x0"
RUN docker-php-ext-install pdo pdo_mysql

COPY ./endstation.conf /etc/apache2/sites-available/endstation.conf

RUN a2dissite 000-default.conf && \
    a2ensite endstation.conf && \
    a2enmod rewrite