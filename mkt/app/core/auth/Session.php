<?php

namespace App\Core;

include_once __DIR__ . "/Cookie.php";
include_once __DIR__ . "/../templates/RedisTemplate.php";

use App\Core\Templates\RedisTemplate;
use App\Core\Cookie;

class Session 
{
    private RedisTemplate $redis;

    public function __construct()
    {
        $this->redis = new RedisTemplate();
    }

    public function getSession(): array|bool {
        $sessionIdCookie = Cookie::getSessionCookie();

        $session = $this->redis->getRedis()->get($sessionIdCookie);

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

    public function setSession(string $email, string $role): string 
    {       
        $EXPIRATION_DAYS = 60*60*24*30; // 30 days in seconds
        $sessionId = bin2hex(random_bytes(32));
        
        // Use setex() instead of set() with TTL
        $this->redis->getRedis()->setex($sessionId, $EXPIRATION_DAYS, "$email, $role");
        
        return $sessionId;
    }

    public function deleteSession(): void
    {
        $sessionIdCookie = Cookie::getSessionCookie();
        $this->redis->getRedis()->del($sessionIdCookie);
    }
}