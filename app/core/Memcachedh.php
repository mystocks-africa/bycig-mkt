<?php

namespace App\Core;

include_once __DIR__ . "/../app/controllers/auth/Controller.php";

use App\Controllers\AuthController;
use Memcached;

class MemcachedH {
    private $memcached;

    public function __construct() 
    {
        $memcached = new Memcached();
        $memcached->addServer('/tmp/memcached.sock', 0);
        $this->memcached = $memcached;
    }

    public function setSession(string $email, string $role) 
    {        
        $EXPIRATION_DAYS = 60*60*24*30; // 30 days in seconds
        $sessionId = bin2hex(random_bytes(32)); // 64 character hex string
        $this->memcached->set($sessionId, "$email, $role", $EXPIRATION_DAYS);
        
        return $sessionId;
    }

    public function getSession() 
    {
        // Check if session_id cookie is set and not empty
        if (empty($_COOKIE["session_id"])) {
            return false;
        }

        $session_id_cookie = $_COOKIE["session_id"];
        $session = $this->memcached->get($session_id_cookie);

        if (!$session) {
            // Clear the session cookie so that the navbar UI is updated
            if (isset($_COOKIE["session_id"])) {
                $auth = new AuthController();
                $auth->clearSessionCookie();
            }

            return false;
        }  

        $parts = explode(",", $session);

        $sessionAssoc = [
            "email" => trim($parts[0]),
            "role" => trim($parts[1])
        ];

        return $sessionAssoc;    
    }

    public function clearSession()
    {
        $session_id = $_COOKIE['session_id'];
        $this->memcached->delete($session_id);
    }
}