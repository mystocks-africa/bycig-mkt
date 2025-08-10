<?php 

namespace App\Core\Auth;

include_once __DIR__ . '/../Memcachedh.php';

use App\Core\Memcachedh;

class Session 
{
    private $memcached;

    public function __construct()
    {
        $this->memcached = new Memcachedh;
    }

    public function setSession(string $email, string $role) 
    {        
        $EXPIRATION_DAYS = 60*60*24*30; // 30 days in seconds
        $sessionId = bin2hex(random_bytes(32)); // 64 character hex string
        $this->memcached->setKeyValue($sessionId, "$email, $role", $EXPIRATION_DAYS);
        
        return $sessionId;
    }

    public function getSession() 
    {
        // Check if session_id cookie is set and not empty
        if (empty($_COOKIE["session_id"])) {
            return false;
        }

        $session_id_cookie = $_COOKIE["session_id"];
        $session = $this->memcached->getKeyValjue($session_id_cookie);

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
        $this->memcached->deleteKeyValue($session_id);
    }
}