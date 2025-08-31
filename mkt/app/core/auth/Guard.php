<?php 

namespace App\Core\Auth;
include_once __DIR__ . "/Session.php";

use App\Core\Session;

// Protects UI based on auth status
class AuthGuard 
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function redirectIfAuth(): void 
    {        
        if ($this->session->getSession()) {
            header("Location: /");
            exit();
        }
    }

    public function redirectIfNotAuth(): void 
    {
        if (!$this->session->getSession()) {
            header("Location: /auth/signin");
            exit();
        }

    }

    public function redirectIfNotClusterLeader(): void 
    {
        if ($this->session->getSession()['role'] != "cluster_leader") {
            $msg = "You are not authenticated for this action.";
            header("Location: /redirect?message=$msg&message_type=error");
            exit();
        }
    }
}