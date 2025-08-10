<?php

namespace App\Core;

include_once __DIR__ . '/../controllers/auth/Controller.php';

use App\Controllers\AuthController;
use Memcached;

class Memcachedh {
    private $memcached;

    public function __construct() 
    {
        $memcached = new Memcached();
        $memcached->addServer('/tmp/memcached.sock', 0);
        $this->memcached = $memcached;
    }

    public function setKeyValue($key, $value, $expiration)
    {
        if ($expiration) $this->memcached->set($key, $value, $expiration);
        else $this->memcached->set($key, $value);
    }

    public function deleteKeyValue($key)
    {
        $this->memcached->delete($key);
    }

    public function getKeyValue($key)
    {
        return $this->memcached->get($key)
    }
}