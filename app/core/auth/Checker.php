<?php 

namespace App\Core\Auth;
include_once __DIR__ . "/Session.php";

use App\Core\Auth\Session;
use Exception;

class Controller
{
    private $session;
    
    public function __construct()
    {
        $session = new Session();
        $this->session = $session->getSession();
    }

    protected static function redirectIfAuth() {
        if ($this->session) {
            header("Location: /auth/signout");
            exit();
        }
    }

    protected static function redirectIfNotAuth($returnSession = false) 
    {
        if (!$this->session) {
            header("Location: /auth/signin");
            exit();
        }

        if ($returnSession) {
            return $session;
        }
    }

    protected static function redirectIfNotClusterLeader() {
        if (!$this->session && $this->session['role'] != "cluster_leader") {
            $msg = "You are not authenticated for this action.";
            header("Location: /redirect?message=$msg&message_type=error");
            exit();
        }

        return $session;
    }
}