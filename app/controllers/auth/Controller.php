<?php

namespace App\Controllers;

include_once __DIR__ . "/Controller.php";
include_once __DIR__ . "/../../models/user/Model.php";
include_once __DIR__. "/../../../utils/memcached.php";

use App\Controller;
use App\Models\User;
use Exception;
use Memcached;

class AuthController extends Controller
{   
    private $memcached;

    public function __construct() 
    {
        $memcached = new Memcached();

        // Connect via Unix socket (local communication)
        $memcached->addServer('/tmp/memcached.sock', 0);
        $this->memcached = $memcached;
    }

    private function assignSession($email, $role) 
    {
        $EXPIRATION_DAYS = 60*60*24*30; // 30 days in seconds
        
        try {
            $session_id = bin2hex(random_bytes(32)); // 64 character hex string
            $this->memcached->set($session_id, "$email, $role", $EXPIRATION_DAYS);
            
            return $session_id;
        } catch(Exception $error) {
            return parent::redirectToResult($error->getMessage(), "error");
        }
    }

    private function assignSessionCookie($session_id) 
    {
        try {
            setcookie('session_id', $session_id, [
                'expires' => time() + (10* 365 * 24 * 60 * 60), // 10 years in seconds
                'path' => '/',
                'httponly' => true,
            ]);
        } catch(Exception $error) {
            return parent::redirectToResult($error->getMessage(), "error");
        }
    }

    private function clearSession() 
    {
        try {
            $session_id = $_COOKIE['session_id'];
            $this->memcached->delete($session_id);
        } catch (Exception $error) {
            return parent::redirectToResult($error->getMessage(), "error");
        }
    }

    public function clearSessionCookie() 
    {
        try {
            setcookie('session_id', '', [
                'expires' => 0,
                'path' => '/',
                'httponly' => true,
            ]);        
        } catch(Exception $error) {
            return parent::redirectToResult($error->getMessage(), "error");
        }
    }

    // Render views
    public function signIn()
    {
        parent::redirectIfAuth();
        parent::render('/auth/signin');
    }

    public function signUp()
    {
        parent::redirectIfAuth();
        parent::render('/auth/signup');
    }

    // Backend logic (post methods)
    public function signInPost() 
    {
        parent::redirectIfAuth();

        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $pwd = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));

        $user = User::findByEmail($email);

        if (isset($user) && password_verify($pwd, $user["pwd"])) {
            $session_id = $this->assignSession($user["email"], $user["role"]);
            $this->assignSessionCookie($session_id);
            parent::redirectToResult("Successfully logged in! Welcome!", "success");
        } else {
            parent::redirectToResult("Problem with logging in. Try again.", "error");
        } 
    }

    public function signUpPost() 
    {
        parent::redirectIfAuth();

        try {
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $pwd = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
            $clusterLeader = filter_input(INPUT_POST, 'cluster_leader', FILTER_SANITIZE_SPECIAL_CHARS);
            $fullName = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS);

            $hashPwd = password_hash($pwd, PASSWORD_DEFAULT);

            $user = new User($email, $hashPwd, $clusterLeader, $fullName);
            $user->createUser();

            parent::redirectToResult("User has been created. You may sign in now.", "success");
        } catch (Exception $error) {
            parent::redirectToResult( $error->getMessage(), "error");
        }
    }
    
    public function signOutPost() 
    {
        parent::redirectIfNotAuth();

        $this->clearSession();
        $this->clearSessionCookie();
        parent::redirectToResult("Signed out successfully!", "success");
    }
}