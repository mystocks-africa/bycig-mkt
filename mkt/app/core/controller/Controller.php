<?php
namespace App\Core;

include_once __DIR__ . "/../auth/Session.php";

use Exception;
use App\Core\Session;
class Controller
{
    public static function render($view, $data = [])
    {
        extract($data);

        include __DIR__ . "/../../views/$view.php";
    }

    public static function redirectToResult($msg, $msgType) 
    {
        try {

            if ($msgType == "success" || $msgType == "error") {
                header("Location: /redirect?message=" . urlencode($msg) . "&message_type=" . urlencode($msgType));
            }

            else {
                throw new Exception("Message type is not valid. It needs to be either success or error");
            }
        } catch(Exception $error) {
            return $error->getMessage();
        }
    }

    public static function redirectIfAuth($returnSession = false) {
        $session = new Session();
        
        if ($session->getSession()) {
            header("Location: /auth/signout");
            exit();
        }

        if ($returnSession) {
            return $session->getSession();
        }
    }

    public static function redirectIfNotAuth($returnSession = false) 
    {
        $session = new Session();
        
        if (!$session->getSession()) {
            header("Location: /auth/signin");
            exit();
        }

        if ($returnSession) {
            return $session->getSession();
        }
    }

    public static function redirectIfNotClusterLeader() {
        $session = self::redirectIfNotAuth(returnSession: true); // redirectIfNotAuth calls getSession internally

        if ($session['role'] != "cluster_leader") {
            $msg = "You are not authenticated for this action.";
            header("Location: /redirect?message=$msg&message_type=error");
            exit();
        }

        return $session;
    }
}