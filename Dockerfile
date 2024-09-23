# Dockerfile

# Base image with PHP 8.x and necessary extensions
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/rule-engine

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    nano \
    libcurl4-openssl-dev \
    libssl-dev \
    libmagickwand-dev \
    --no-install-recommends

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip sockets

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create New User
RUN useradd -ms /bin/bash ubuntu

# Add user to 'sudo' group
RUN usermod -aG docker ubuntu

# Set Ownership and copy code (Prod only)
COPY --chown=ubuntu:docker . /var/www/rule-engine

# Change current user
USER ubuntu

# Install PHP dependencies using Composer
RUN composer install --no-dev --optimize-autoloader

# Set Ownership
#RUN chown -R ubuntu: /var/www

# Expose port 80 and start php-fpm server
EXPOSE 80

CMD ["php-fpm"]
