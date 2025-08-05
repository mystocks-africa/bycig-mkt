<?php

namespace App;
include_once __DIR__ . "/../../utils/memcached.php";

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

        // check if session_id cookie is set and not empty
        if (empty($_COOKIE["session_id"])) {
            return false;
        }

        $session_id_cookie = $_COOKIE["session_id"];
        $session = $memcached->get($session_id_cookie);
        return $session;
    }

    protected static function redirectIfAuth() {
        $session = self::getSession();

        if ($session) {
            header("Location: signout");
            exit();
        }

        return $session;
    }

    protected static function redirectIfNotAuth() 
    {
        $session = self::getSession();
        
        if (!$session) {
            header("Location: signin");
            exit();
        }

    }


    protected static function redirectToResult($msg, $msgType) 
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
            header("Location: index.php");
            exit;  
        }

        header("Location: redirect?message=$msg&message_type=$msgType");
    }
}