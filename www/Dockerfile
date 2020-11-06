# setup Composer
FROM composer:latest as builder

# copy the contents of the website
COPY ./ /var/www/html/

# change the directory to the temporary folder
WORKDIR /var/www/html

# install the needed libraries
RUN composer install

# setup Apache with PHP
FROM php:7.3-apache

# enable MySQL for use in the application
RUN docker-php-ext-install mysqli pdo pdo_mysql

# copy the libraries created to the project folder
COPY --from=builder /var/www/html /home/drixlerangelo/www/digital-bookmark

# change the directory to the project folder
WORKDIR /home/drixlerangelo/www/digital-bookmark

# make the applicable permissions
RUN chown -R www-data:www-data /home/drixlerangelo/www/digital-bookmark/storage && \
    chown -R www-data:www-data /home/drixlerangelo/www/digital-bookmark/bootstrap/cache && \
    chmod -R 777 /home/drixlerangelo/www/digital-bookmark/storage && \
    chmod -R 777 /home/drixlerangelo/www/digital-bookmark/bootstrap/cache

# update the system
RUN apt-get update -y && apt-get upgrade -y

# enable ssl
RUN a2enmod ssl

# enable rewrite
RUN a2enmod rewrite

# restart apache
RUN service apache2 restart
