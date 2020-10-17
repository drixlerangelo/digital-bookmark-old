# use a apache with php docker image
FROM php:7.3-apache

# enable mysql for use in the application
RUN docker-php-ext-install mysqli pdo pdo_mysql

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
