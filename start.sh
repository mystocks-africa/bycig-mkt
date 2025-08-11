#!/bin/bash

# Start memcached with UNIX socket
memcached -d -s /tmp/memcached.sock -a 770 -m 64 -vv

# Wait a moment to ensure memcached is up
sleep 2

# Start PHP built-in web server (for development only)
php -S 0.0.0.0:8000 -t public
