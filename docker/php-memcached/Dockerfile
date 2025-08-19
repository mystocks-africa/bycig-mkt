FROM php:8.2-cli

# Install system dependencies for memcached and composer
RUN apt-get update && apt-get install -y \
    libmemcached-dev \
    zlib1g-dev \
    libssl-dev \
    memcached \
    supervisor \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install memcached PHP extension via PECL
RUN pecl install memcached \
    && docker-php-ext-enable memcached

RUN docker-php-ext-install mysqli

# Create supervisor config directory
RUN mkdir -p /etc/supervisor/conf.d

# Create supervisor configuration for memcached and PHP server
RUN echo '[supervisord]' > /etc/supervisor/conf.d/supervisord.conf && \
    echo 'nodaemon=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '[program:memcached]' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'command=memcached -m 64 -p 11211 -u root -l 0.0.0.0' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autostart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stderr_logfile=/var/log/memcached.err.log' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stdout_logfile=/var/log/memcached.out.log' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo '[program:php-server]' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'command=php -S 0.0.0.0:9999' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'directory=/usr/src/myapp/public' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autostart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'autorestart=true' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stderr_logfile=/var/log/php-server.err.log' >> /etc/supervisor/conf.d/supervisord.conf && \
    echo 'stdout_logfile=/var/log/php-server.out.log' >> /etc/supervisor/conf.d/supervisord.conf

# Set working directory
WORKDIR /usr/src/myapp

# Copy composer.json first (if it exists) for better Docker layer caching
COPY composer.json* ./

# Install PHPMailer and other dependencies
RUN composer require phpmailer/phpmailer || composer init --no-interaction && composer require phpmailer/phpmailer

# Copy application directories
COPY ./app ./app/
COPY ./public ./public/
COPY ./utils ./utils/

# Copy any remaining files
COPY . .

# Expose the PHP development server port
EXPOSE 9999

# Start supervisor to manage both memcached and PHP server
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]