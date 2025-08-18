<?php

namespace App\Core;
include_once __DIR__ . "/../core/templates/MemcachedTemplate.php";

use App\Core\Templates\MemcachedTemplate;

class VerificationCode extends MemcachedTemplate
{
    public static function generateCode($email) {
        $memcached = parent::getMemcached();

        $code = rand(10000,99999);

        $expiration = 5 * 60; // 5 minutes in seconds
        $memcached->set($email, $code, $expiration);

        parent::removeMemcached($memcached);

        return $code;
    }

    public static function getCode($email, $code) {
        $memcached = parent::getMemcached();
        return $memcached->get($email, $code) ?? null;
    }
}