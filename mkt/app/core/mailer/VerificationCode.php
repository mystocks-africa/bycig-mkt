<?php

namespace App\Core;
include_once __DIR__ . "/../templates/RedisTemplate.php";

use App\Core\Templates\RedisTemplate;

class VerificationCode 
{
    public static function generateCode($email) {
        $redis = new RedisTemplate();

        $code = rand(10000,99999);

        $expiration = 5 * 60; // 5 minutes in seconds
        
        // Use setex() instead of set() with TTL
        $redis->getRedis()->setex($email, $expiration, $code);

        return $code;
    }

    public static function verifyCode($email, $code) {
        $redis = new RedisTemplate();
        $storedCode = $redis->getRedis()->get($email);
        
        // Use del() instead of delete() - Redis command is DEL
        $redis->getRedis()->del($email);
        
        return $storedCode == $code; // == disregards type but not the actual value within it
    }
}