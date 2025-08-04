<?php
try {
    $memcached = new Memcached();

    // Connect via Unix socket (local communication)
    $memcached->addServer('/tmp/memcached.sock', 0);
    
} catch (Exception $error) {
    echo $error->getMessage();
}