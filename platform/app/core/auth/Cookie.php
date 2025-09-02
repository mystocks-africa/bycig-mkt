<?php
namespace App\Core\Auth;

class Cookie 
{
    public static function getSessionCookie(): string
    {   
        $sessionIdCookie = $_COOKIE["session_id"] ?? "";
        return $sessionIdCookie;
    }
    
    public static function assignSessionCookie(string $session_id): void 
    {
        setcookie('session_id', $session_id, [
            'expires' => time() + (10 * 365 * 24 * 60 * 60), // 10 years
            'path' => '/',
            'httponly' => true,
        ]);
    }

    public static function clearSessionCookie(): void 
    {
        setcookie('session_id', '', [
            'expires' => 0,
            'path' => '/',
            'httponly' => true,
        ]);        
    }
}