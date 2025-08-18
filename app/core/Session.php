<?php

namespace App\Core;

include_once __DIR__ . "/../core/Cookie.php";
include_once __DIR__ . "/../core/templates/MemcachedTemplate.php";

use App\Core\Templates\MemcachedTemplate;
use App\Core\Cookie;

class Session extends MemcachedTemplate {
    public static function getSession() {
        $memcached = parent::getMemcached();

        if (empty($_COOKIE["session_id"])) {
            parent::removeMemcached($memcached);
            return false;
        }

        $session_id_cookie = $_COOKIE["session_id"];
        $session = $memcached->get($session_id_cookie);

        parent::removeMemcached($memcached);

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
        $memcached = parent::getMemcached();

        $EXPIRATION_DAYS = 60*60*24*30;
        $sessionId = bin2hex(random_bytes(32));
        $memcached->set($sessionId, "$email, $role", $EXPIRATION_DAYS);
        
        parent::removeMemcached($memcached);
        return $sessionId;
    }
}
