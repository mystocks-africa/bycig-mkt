<?php

namespace App;
include_once __DIR__ . "/../../utils/memcached.php";
include_once __DIR__ . "/../controllers/auth/Controller.php";

use App\Controllers\AuthController;
use Exception;

class Controller
{
    protected static function render($view, $data = [])
    {
        extract($data);

        include __DIR__ . "/../views/$view.php";
    }

    private static function getSession() 
    {
        global $memcached;

        // Check if session_id cookie is set and not empty
        if (empty($_COOKIE["session_id"])) {
            return false;
        }

        $session_id_cookie = $_COOKIE["session_id"];
        $session = $memcached->get($session_id_cookie);

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

    protected static function redirectIfAuth() {
        $session = self::getSession();

        if ($session) {
            header("Location: /auth/signout");
            exit();
        }
    }

    protected static function redirectIfNotAuth($returnSession = false) 
    {
        $session = self::getSession();
        
        if (!$session) {
            header("Location: /auth/signin");
            exit();
        }

        if ($returnSession) {
            return $session;
        }
    }

    protected static function redirectToResult($msg, $msgType) 
    {
        try {

            if ($msgType == "success" || $msgType == "error") {
                header("Location: /redirect?message=$msg&message_type=$msgType");
            }

            else {
                throw new Exception("Message type is not valid. It needs to be either success or error");
            }
        } catch(Exception $error) {
            return $error->getMessage();
        }
    }
}