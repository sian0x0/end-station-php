FROM php:8.3-apache
LABEL authors="sian0x0"
# install system and extensions
RUN apt-get update && apt-get install -y libzip-dev && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install zip pdo pdo_mysql

COPY endstation.conf /etc/apache2/sites-available/endstation.conf

RUN a2dissite 000-default.conf && \
    a2ensite endstation.conf && \
    a2enmod rewrite

## give /data directory permissions to Apache webserver user (www-data) - REMOVED - confused between host and container permissions?
#RUN mkdir -p /var/www/endstation/data/txt_latest && \
#    chown -R www-data:www-data /var/www/endstation/data && \
#    chmod -R 755 /var/www/endstation/data
