FROM ubuntu:22.04

ENV DEBIAN_FRONTEND=noninteractive

# Install dependencies including zlib development headers
RUN apt-get update && apt-get install -y \
    php-cli \
    php-pear \
    php-dev \
    libmemcached-dev \
    memcached \
    libevent-dev \
    zlib1g-dev \
    net-tools \
    curl \
    nano \
    && pecl install memcached \
    && echo "extension=memcached.so" > /etc/php/8.1/cli/conf.d/20-memcached.ini \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY ./public ./public

EXPOSE 9999

# Start both memcached and PHP dev server
CMD memcached -u root -d && php -S 0.0.0.0:9999 -t /app/public