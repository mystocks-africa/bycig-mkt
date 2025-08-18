<?php
namespace App\Core;

class Cookie 
{
    public static function assignSessionCookie($session_id) 
    {
        setcookie('session_id', $session_id, [
            'expires' => time() + (10 * 365 * 24 * 60 * 60), // 10 years
            'path' => '/',
            'httponly' => true,
        ]);
    }

    public static function clearSessionCookie() 
    {
        setcookie('session_id', '', [
            'expires' => 0,
            'path' => '/',
            'httponly' => true,
        ]);        
    }
}