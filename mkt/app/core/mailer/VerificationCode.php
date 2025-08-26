<?php

namespace App\Core;
include_once __DIR__ . "/../core/templates/RedisTemplate.php";

use App\Core\Templates\RedisTemplate;

class VerificationCode extends RedisTemplate
{
    public static function generateCode($email) {
        $redis = parent::getRedis();

        $code = rand(10000,99999);

        $expiration = 5 * 60; // 5 minutes in seconds
        
        // Use setex() instead of set() with TTL
        $redis->setex($email, $expiration, $code);

        return $code;
    }

    public static function verifyCode($email, $code) {
        $redis = parent::getRedis();
        $storedCode = $redis->get($email);
        
        // Use del() instead of delete() - Redis command is DEL
        $redis->del($email);
        
        return $storedCode == $code; // == disregards type but not the actual value within it
    }
}