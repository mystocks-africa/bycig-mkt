<?php

namespace App\Controllers;

include_once __DIR__ . "/Controller.php";
include_once __DIR__ . "/../../models/user/Model.php";
include_once __DIR__ . "/../../core/Memcachedh.php";

use App\Controller;
use App\Models\UserModel;
use App\Core\MemcachedH;
use Exception;

class AuthController extends Controller
{   
    private function assignSessionCookie($session_id) 
    {
        try {
            setcookie('session_id', $session_id, [
                'expires' => time() + (10 * 365 * 24 * 60 * 60), // 10 years in seconds
                'path' => '/',
                'httponly' => true,
            ]);
        } catch (Exception $error) {
            return parent::redirectToResult($error->getMessage(), "error");
        }
    }

    // Public because it's needed in memcached handler for sessions 
    public function clearSessionCookie() 
    {
        try {
            setcookie('session_id', '', [
                'expires' => 0,
                'path' => '/',
                'httponly' => true,
            ]);        
        } catch (Exception $error) {
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
        $clusterLeaders = UserModel::findAllClusterLeaders();

        // O(n)/linear time complexity is fine here because cluster leaders length will always be small
        $clusterLeaderEmails = [];

        if (!empty($clusterLeaders) && is_array($clusterLeaders)) {
            foreach ($clusterLeaders as $leader) {
                if (isset($leader['email'])) {
                    $clusterLeaderEmails[] = $leader['email'];
                }
            }
        }

        parent::render('/auth/signup', [
            'clusterLeaderEmails' => $clusterLeaderEmails
        ]);
    }

    public function signInPost() 
    {
        parent::redirectIfAuth();

        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $pwd = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));

        $user = UserModel::findByEmail($email);

        if (isset($user) && password_verify($pwd, $user["pwd"])) {
            $memcachedH = new MemcachedH();
            $sessionId = $memcachedH->setSession($user["email"], $user["role"]);
            $this->assignSessionCookie($sessionId);
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

            $user = new UserModel($email, $hashPwd, $clusterLeader, $fullName);
            $user->createUser();

            parent::redirectToResult("User has been created. You may sign in now.", "success");
        } catch (Exception $error) {
            $msg = "There has been an error in signing up.";
            parent::redirectToResult($msg, "error");
        }
    }
    
    public function signOutPost() 
    {
        parent::redirectIfNotAuth();
        $memcachedH = new MemcachedH();
        $memcachedH->clearSession();
        $this->clearSessionCookie();
        parent::redirectToResult("Signed out successfully!", "success");
    }

    public function updatePassword()
    {
        $session = parent::redirectIfNotAuth(returnSession: true);
        $newPwd = filter_input(INPUT_POST, 'new_pwd', FILTER_SANITIZE_SPECIAL_CHARS);
        $hashedPwd = password_hash($newPwd);
        User::updatePwd($session['email'], $hashedPwd);
    }
}