# setup Composer
FROM composer:latest as builder

# copy the contents of the website
COPY ./www/ /var/www/html/

# change the directory to the temporary folder
WORKDIR /var/www/html

# install the needed libraries
RUN composer install

# setup Apache with PHP
FROM php:7.4-apache

# update the system
RUN apt-get update -y && apt-get upgrade -y

# install SQLite
RUN apt-get install -y sqlite3 libsqlite3-dev

# copy the initial operation scripts
COPY ./database/scripts/ /home/user/database/scripts/

# make a directory for saving the database
RUN mkdir -p /home/user/database/storage/

# make the applicable permissions
RUN chown -R www-data:www-data /home/user/database/storage && \
    chmod -R 755 /home/user/database/storage

# change the directory to the database
WORKDIR /home/user/database/storage

# copy the database file
COPY ./database/storage/digital_bookmark.db digital_bookmark.db

# enable SQLite for use in the application
RUN docker-php-ext-install pdo pdo_sqlite

# copy the libraries created to the project folder
COPY --from=builder /var/www/html /home/user/www

# change the directory to the project folder
WORKDIR /home/user/www

# make the applicable permissions
RUN chown -R www-data:www-data /home/user/www/storage && \
    chown -R www-data:www-data /home/user/www/bootstrap/cache && \
    chmod -R 777 /home/user/www/storage && \
    chmod -R 777 /home/user/www/bootstrap/cache

# create the structure of the database
RUN php artisan migrate:fresh

# execute the initial operation for the database
RUN sqlite3 /home/user/database/storage/digital_bookmark.db < /home/user/database/scripts/init.sql

# enable ssl
RUN a2enmod ssl

# enable rewrite
RUN a2enmod rewrite

# restart apache
RUN service apache2 restart
