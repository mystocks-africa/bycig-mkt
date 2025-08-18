<?php

namespace App\Core\Templates;

class MemcachedTemplate {
    protected static function getMemcached() {
        $memcached = new Memcached();
        $memcached->addServer('/tmp/memcached.sock', 0);

        return $memcached;
    }

    protected static function removeMemcached($memcached) {
        if ($memcached instanceof Memcached) {
            $memcached->quit(); // politely close connection
            $memcached = null; // free object reference
        }
    }
}
