# use a apache with php docker image
FROM php:7.3-apache

# set the directory to root
WORKDIR /

# update the system
RUN apt-get update -y && apt-get upgrade -y

# enable ssl
RUN a2enmod ssl

# enable rewrite
RUN a2enmod rewrite

# restart apache
RUN service apache2 restart

# put composer inside the app
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
