FROM php:8.2-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    wget \
    vim \
    git \
    zip \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libpng-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install mysqli pdo_mysql gd zip pcntl exif

# Enable PHP extensions
RUN docker-php-ext-enable mysqli

# Enable Apache modules
RUN a2enmod headers expires rewrite

# Install and enable Xdebug
RUN pecl install xdebug-3.2.2 && docker-php-ext-enable xdebug \
    && { \
        echo "xdebug.mode=debug"; \
        echo "xdebug.client_host=host.docker.internal"; \
    } > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN a2dismod mpm_event mpm_worker && a2enmod mpm_prefork

# The rest of your Dockerfile...
# Copy custom apache config file and check for syntax errors
COPY apache.config /etc/apache2/apache2.conf
RUN apachectl configtest

# Set the working directory in the container
WORKDIR /var/www/html

# The CMD command is used to set the default command to be executed when running a container.
# There is no need to explicitly restart Apache, as it will be started automatically by the base image's CMD.