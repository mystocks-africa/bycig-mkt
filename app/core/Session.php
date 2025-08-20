<?php

namespace App\Core;

include_once __DIR__ . "/../core/Cookie.php";
include_once __DIR__ . "/../core/templates/RedisTemplate.php";

use App\Core\Templates\RedisTemplate;
use App\Core\Cookie;

class Session extends RedisTemplate 
{
    public static function getSession() {
        $redis = parent::getRedis();

        $session_id_cookie = $_COOKIE["session_id"] ?? "";

        $session = $redis->get($session_id_cookie);

        if (!$session) {
            Cookie::clearSessionCookie();
            return false;
        }

        $parts = explode(",", $session);
        return [
            "email" => trim($parts[0]),
            "role" => trim($parts[1])
        ];
    }

    public static function setSession(string $email, string $role) 
    {       
        $redis = parent::getredis();

        $EXPIRATION_DAYS = 60*60*24*30; // 30 days in seconds
        $sessionId = bin2hex(random_bytes(32));
        
        // Use setex() instead of set() with TTL
        $redis->setex($sessionId, $EXPIRATION_DAYS, "$email, $role");
        
        return $sessionId;
    }
}