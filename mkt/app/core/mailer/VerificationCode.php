<?php

namespace App\Core;
include_once __DIR__ . "/../templates/RedisTemplate.php";

use App\Core\Templates\RedisTemplate;

class VerificationCode extends RedisTemplate
{
    public static function generateCode($email) {
        $redis = new RedisTemplate()->getRedis();

        $code = rand(10000,99999);

        $expiration = 5 * 60; // 5 minutes in seconds
        
        // Use setex() instead of set() with TTL
        $redis->setex($email, $expiration, $code);

        return $code;
    }

    public static function verifyCode($email, $code) {
        $redis = new RedisTemplate()->getRedis();
        $storedCode = $redis->get($email);
        
        // Use del() instead of delete() - Redis command is DEL
        $redis->del($email);
        
        return $storedCode == $code; // == disregards type but not the actual value within it
    }
}