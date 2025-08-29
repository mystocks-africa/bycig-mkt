<?php 

namespace App\Core\Auth;
include_once __DIR__ . "/Session.php";

use App\Core\Session;

// Protects UI based on auth status
class AuthGuard 
{
    public static function redirectIfAuth(Session $session): void 
    {        
        if ($session->getSession()) {
            header("Location: /auth/signout");
            exit();
        }
    }

    public static function redirectIfNotAuth(Session $session): void 
    {
        if (!$session->getSession()) {
            header("Location: /auth/signin");
            exit();
        }

    }

    public static function redirectIfNotClusterLeader(Session $session): void 
    {
        if ($session->getSession()['role'] != "cluster_leader") {
            $msg = "You are not authenticated for this action.";
            header("Location: /redirect?message=$msg&message_type=error");
            exit();
        }
    }
}