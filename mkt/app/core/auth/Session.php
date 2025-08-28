<?php

namespace App\Core;

include_once __DIR__ . "/Cookie.php";
include_once __DIR__ . "/../templates/RedisTemplate.php";

use App\Core\Templates\RedisTemplate;
use App\Core\Cookie;
use Predis\Client;

class Session extends RedisTemplate 
{

    private Client $redis;

    public function __construct()
    {
        $redisObj = new RedisTemplate();
        $this->redis = $redisObj->getRedis();
    }

    public function getSession() {
        $session_id_cookie = $_COOKIE["session_id"] ?? "";

        $session = $this->redis->get($session_id_cookie);

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
        $EXPIRATION_DAYS = 60*60*24*30; // 30 days in seconds
        $sessionId = bin2hex(random_bytes(32));
        
        // Use setex() instead of set() with TTL
        $this->redis->setex($sessionId, $EXPIRATION_DAYS, "$email, $role");
        
        return $sessionId;
    }

    public function deleteSession(string $email): void
    {
        $this->redis->del($email);
    }
}