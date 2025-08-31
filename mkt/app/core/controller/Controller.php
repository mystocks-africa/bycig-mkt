<?php
namespace App\Core;

include_once __DIR__ . "/../auth/Session.php";

use Exception;
use App\Core\Session;
class Controller
{
    public static function render(string $view, array $data = []): void
    {
        extract($data);

        include __DIR__ . "/../../views/$view.php";
    }

    public static function redirectToResult(string $msg, string $msgType): ?string 
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
        
        return null;
    }

    public static function redirectIfAuth(bool $returnSession = false): ?array {
        $session = new Session();
        
        if ($session->getSession()) {
            header("Location: /auth/signout");
            exit();
        }

        if ($returnSession) {
            return $session->getSession();
        }
        
        return null;
    }

    public static function redirectIfNotAuth(bool $returnSession = false): ?array 
    {
        $session = new Session();
        
        if (!$session->getSession()) {
            header("Location: /auth/signin");
            exit();
        }

        if ($returnSession) {
            return $session->getSession();
        }
        
        return null;
    }

    public static function redirectIfNotClusterLeader(): array {
        $session = self::redirectIfNotAuth(returnSession: true); // redirectIfNotAuth calls getSession internally

        if ($session['role'] != "cluster_leader") {
            $msg = "You are not authenticated for this action.";
            header("Location: /redirect?message=$msg&message_type=error");
            exit();
        }

        return $session;
    }
}