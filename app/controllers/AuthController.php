<?php
namespace App\Controllers;

include_once __DIR__ . "/Controller.php";
include_once __DIR__ . "/../models/User.php";
include_once __DIR__. "/../../utils/memcached.php";

use App\Controller;
use App\Models\User;
use Exception;

class AuthController extends Controller
{   
    private $memcached;

    public function __construct() {
        global $memcached;
        $this->memcached = $memcached;
    }

    private function assignSession($email, $role) {
        $EXPIRATION_DAYS = 60*60*24*30; // 30 days in seconds
        try {
            $session_id = bin2hex(random_bytes(32)); // 64 character hex string
            $this->memcached->set($session_id, "$email, $role", $EXPIRATION_DAYS);
            
            return $session_id;
        } catch(Exception $error) {
            return $error->getMessage();
        }
    }

    private function assignSessionCookie($session_id) {
        try {
            setcookie('session_id', $session_id);
        } catch(Exception $error) {
            return $error->getMessage();
        }
    }

    private function clearSession($session_id) {
        try {
            $this->memcached->delete($session_id);
            return true;
        } catch (Exception $error) {
            return $error->getMessage();
        }
    }

    private function clearSessionCookie() {
        try {
            setcookie('session_id', '');
        } catch(Exception $error) {
            return $error->getMessage();
        }
    }

    public function signIn()
    {
        parent::redirectAuth();
        parent::render('signIn');
    }

    public function signInPost() {
        parent::redirectAuth();

        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $pwd = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));

        $user = User::findByEmail($email);

        if (isset($user) && password_verify($pwd, $user["pwd"])) {
            $session_id = $this->assignSession($user["email"], $user["role"]);
            $this->assignSessionCookie($session_id);
        } else {
            parent::redirectToResult("Problem with logging in. Try again.", "error");
        } 
    }

    public function signUp()
    {
        parent::redirectAuth();
        parent::render('signUp');
    }

    public function signUpPost() 
    {
        parent::redirectAuth();
    }

    public function signOut() 
    {
        parent::redirectNotAuth();
        parent::render('signOut');
    }
}