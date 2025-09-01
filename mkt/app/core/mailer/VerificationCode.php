<?php
namespace App\Core\Mailers;

use App\Core\Templates\RedisTemplate;

class VerificationCode 
{
    public static function generateCode(string $email): int {
        $redis = new RedisTemplate();
        $code = rand(10000,99999);
        $expiration = 5 * 60; // 5 minutes in seconds
        $redis->getRedis()->setex($email, $expiration, $code);

        return $code;
    }

    public static function verifyCode(string $email, string $code): bool {
        $redis = new RedisTemplate();
        $storedCode = $redis->getRedis()->get($email);
        
        $redis->getRedis()->del($email);
        
        return $storedCode == $code; // == disregards type but not the actual value within it
    }
}