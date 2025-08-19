FROM ubuntu:22.04

# Noninteractive mode to suppress prompts
ENV DEBIAN_FRONTEND=noninteractive

# Install dependencies
RUN apt-get update && apt-get install -y \
    php-cli \
    php-pear \
    php-dev \
    libmemcached-dev \
    memcached \
    libevent-dev \
    net-tools \
    curl \
    nano \
    && pecl install memcached \
    && echo "extension=memcached.so" > /etc/php/8.1/cli/conf.d/20-memcached.ini

# Create directory for socket
RUN mkdir -p /tmp/memcached.sock

# Add project files
WORKDIR /app
COPY ./public ./public
COPY start.sh .

# Make script executable
RUN chmod +x start.sh

# Expose PHP dev server port
EXPOSE 9999

# Start script runs both memcached and PHP
CMD ["./start.sh"]