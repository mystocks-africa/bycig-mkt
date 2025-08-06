<?php

namespace App;
include_once __DIR__ . "/../core/Memcachedh.php";

use App\Core\Memcachedh;
use Exception;

class Controller
{
    protected static function render($view, $data = [])
    {
        extract($data);

        include __DIR__ . "/../views/$view.php";
    }

    protected static function redirectIfAuth() {
        $memcachedH = new MemcachedH();
        $session = $memcachedH->getSession();

        if ($session) {
            header("Location: /auth/signout");
            exit();
        }
    }

    protected static function redirectIfNotAuth($returnSession = false) 
    {
        $memcachedH = new MemcachedH();
        $session = $memcachedH->getSession();

        if (!$session) {
            header("Location: /auth/signin");
            exit();
        }

        if ($returnSession) {
            return $session;
        }
    }

    protected static function redirectIfNotClusterLeader() {
        $session = self::redirectIfNotAuth(returnSession: true);

        if ($session['role'] != "cluster_leader") {
            $msg = "You are not authenticated for this action.";
            header("Location: /redirect?message=$msg&message_type=error");
            exit();
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