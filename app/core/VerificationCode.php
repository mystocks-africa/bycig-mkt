<?php

namespace App\Core;
include_once __DIR__ . "/../core/templates/RedisTemplate.php";

use App\Core\Templates\RedisTemplate;

class VerificationCode extends RedisTemplate
{
    public static function generateCode($email) {
        $memcached = parent::getRedis();

        $code = rand(10000,99999);

        $expiration = 5 * 60; // 5 minutes in seconds
        $memcached->set($email, $code, $expiration);


        return $code;
    }

    public static function verifyCode($email, $code) {
        $memcached = parent::getRedis();
        $storedCode = $memcached->get($email);
        $memcached->delete($email);
        
        return $storedCode == $code; // == disregards type but not the actual value within it
    }

}