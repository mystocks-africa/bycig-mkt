<?php

namespace App\Core;

include_once __DIR__ . "/Cookie.php";
include_once __DIR__ . "/../templates/RedisTemplate.php";

use App\Core\Templates\RedisTemplate;
use App\Core\Cookie;

class Session extends RedisTemplate 
{

    private RedisTemplate $redis;

    public function __construct()
    {
        $this->redis = new RedisTemplate();
    }

    public function getSession() {
        $redis = $this->redis->getRedis();

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

    public function setSession(string $email, string $role) 
    {       
        $redis = $this->redis->getRedis();

        $EXPIRATION_DAYS = 60*60*24*30; // 30 days in seconds
        $sessionId = bin2hex(random_bytes(32));
        
        // Use setex() instead of set() with TTL
        $redis->setex($sessionId, $EXPIRATION_DAYS, "$email, $role");
        
        return $sessionId;
    }

    public function deleteSession(string $email): void
    {
        $redis = $this->redis->getRedis();
        $redis->del($email);
    }
}