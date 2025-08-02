<?php
try {
    $memcached = new Memcached();
    $memcached->addServer('/tmp/memcached.sock', 0);
} catch (Exception $error) {
    echo $error->getMessage();
}