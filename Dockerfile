# setup Composer
FROM composer:latest as builder

# copy the contents of the website
COPY ./www/ /var/www/html/

# change the directory to the temporary folder
WORKDIR /var/www/html

# install the needed libraries
RUN composer install --ignore-platform-reqs

# setup Apache with PHP
FROM php:7.4-apache

# update the system
RUN apt-get update -y && apt-get upgrade -y

# install SQLite
RUN apt-get install -y sqlite3 libsqlite3-dev

# copy the initial operation scripts
COPY ./database/scripts/ /home/user/database/scripts/

# change the directory to the database scripts folder
WORKDIR /home/user/database/scripts

# copy the example script
RUN cp -n init.sql.example init.sql

# make a directory for saving the database
RUN mkdir -p /home/user/database/storage/

# change the directory to the database
WORKDIR /home/user/database/storage

# copy the database file
COPY ./database/storage/digital_bookmark.db digital_bookmark.db

# make the applicable permissions
RUN chown -R www-data:www-data /home/user/database/storage && \
    chmod -R 755 /home/user/database/storage

# enable SQLite for use in the application
RUN docker-php-ext-install pdo pdo_sqlite

# retrieve the password for the database
ARG DB_PASSWORD

# hide the password in a file
RUN echo "$DB_PASSWORD" > /home/user/database/storage/DB_PASSWORD

# copy the libraries created to the project folder
COPY --from=builder /var/www/html /home/user/www

# change the directory to the project folder
WORKDIR /home/user/www

# make the applicable permissions
RUN chown -R www-data:www-data /home/user/www/storage && \
    chown -R www-data:www-data /home/user/www/bootstrap/cache && \
    chmod -R 777 /home/user/www/storage && \
    chmod -R 777 /home/user/www/bootstrap/cache

# copy the example environment file
RUN cp -n .env.example .env

# create a new key when there's none
RUN php artisan key:generate

# create the structure of the database
RUN php artisan migrate:fresh

# execute the initial operation for the database
RUN sqlite3 /home/user/database/storage/digital_bookmark.db < /home/user/database/scripts/init.sql

# create a folder to place keys and certificates
RUN mkdir -p /var/imported/ssl

# change the directory to the SSL folder
WORKDIR /var/imported/ssl

# copy the provided SSL config
COPY ./ssl/openssl.cnf ./openssl.cnf

# initialize the SSL parameters
ARG OPENSSL_COUNTRY_CODE \
    OPENSSL_STATE \
    OPENSSL_LOCALITY \
    OPENSSL_ORG \
    OPENSSL_UNIT \
    OPENSSL_COMMON_NAME

# apply the SSL parameters to the config file
RUN sed -i -e "s/OPENSSL_COUNTRY_CODE/$OPENSSL_COUNTRY_CODE/g" \
    -e "s/OPENSSL_STATE/$OPENSSL_STATE/g" \
    -e "s/OPENSSL_LOCALITY/$OPENSSL_LOCALITY/g" \
    -e "s/OPENSSL_ORG/$OPENSSL_ORG/g" \
    -e "s/OPENSSL_UNIT/$OPENSSL_UNIT/g" \
    -e "s/OPENSSL_COMMON_NAME/$OPENSSL_COMMON_NAME/g" openssl.cnf

# generate the SSL key and certificate
RUN openssl req \
    -x509 \
    -nodes \
    -out cert.pem \
    -keyout key.pem \
    -days 365 \
    -config openssl.cnf

# enable ssl
RUN a2enmod ssl

# enable rewrite
RUN a2enmod rewrite

# restart apache
RUN service apache2 restart
