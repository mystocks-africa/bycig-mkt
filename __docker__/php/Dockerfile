FROM php:8.2-cli

# Install FTP extension
RUN docker-php-ext-install ftp

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
        libzip-dev \
        libonig-dev \
        libpng-dev \
        libxml2-dev \
        unzip \
        git \
        curl \
    && docker-php-ext-install mysqli pdo pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy project files
COPY ./mkt /app/

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

EXPOSE 9999

CMD ["php", "-S", "0.0.0.0:9999", "-t", "mkt/public"]
