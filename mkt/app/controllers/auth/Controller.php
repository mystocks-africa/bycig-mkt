<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../core/auth/Cookie.php";
include_once __DIR__ . "/../../core/mailer/VerificationCode.php";
include_once __DIR__ . "/../../core/mailer/HTMLMessages.php";
include_once __DIR__ . "/../../core/mailer/Mailer.php";
include_once __DIR__ . "/../../models/user/Entity.php";
include_once __DIR__ . "/../../models/user/Repository.php";

use App\Core\Controller;
use App\Core\Session;
use App\Core\VerificationCode;
use App\DbTemplate;
use App\Models\Entity\UserEntity;
use App\Models\Repository\UserRepository;
use App\Core\Cookie;
use Exception;
use App\Core\Mailer;
use HTMLMessages;

class AuthController
{   
    private UserRepository $userRepository;
    private DbTemplate $db;
    private Session $session;

    public function __construct() {
        $this->db = new DbTemplate();
        $this->userRepository = new UserRepository($this->db->getPdo());    
        $this->session = new Session();
    }

    public function signIn()
    {
        Controller::redirectIfAuth();
        Controller::render('/auth/signin');
    }

    public function signUp()
    {
        Controller::redirectIfAuth();
        $clusterLeaders = $this->userRepository->findAllClusterLeaders();

        // O(n) is fine because cluster leaders will always be small
        $clusterLeaderEmails = [];

        if (!empty($clusterLeaders) && is_array($clusterLeaders)) {
            foreach ($clusterLeaders as $leader) {
                if (isset($leader['email'])) {
                    $clusterLeaderEmails[] = $leader['email'];
                }
            }
        }

        Controller::render('/auth/signup', [
            'clusterLeaderEmails'=> $clusterLeaderEmails
        ]);
    }

    public function forgotPwd()
    {
        Controller::render("auth/forgot-pwd");
    }

    public function updatePwd() 
    {
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_SPECIAL_CHARS);
        Controller::render("auth/update-pwd", [
            "code"=>$code
        ]);
    }

    public function processSignIn() 
    {
        Controller::redirectIfAuth();

        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        $pwd   = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));

        $user = $this->userRepository->findByEmail($email);

        if (isset($user) && password_verify($pwd, $user["pwd"])) {
            $sessionId  = $this->session->setSession($user["email"], $user["role"]);
            Cookie::assignSessionCookie($sessionId);
            Controller::redirectToResult("Successfully logged in! Welcome!", "success");
        } else {
            Controller::redirectToResult("Problem with logging in. Try again.", "error");
        } 
    }

    public function processSignUp() 
    {
        Controller::redirectIfAuth();

        try {
            $email        = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $pwd          = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
            $clusterLeader= filter_input(INPUT_POST, 'cluster_leader', FILTER_SANITIZE_SPECIAL_CHARS);
            $fullName     = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS);

            $hashPwd = password_hash($pwd, PASSWORD_DEFAULT);

            $userEntity = new UserEntity(
                $email, 
                $hashPwd, 
                $clusterLeader, 
                $fullName
            );

            $this->userRepository->save($userEntity);

            Controller::redirectToResult("User has been created. You may sign in now.", "success");
        } catch (Exception $error) {
            $msg = "There has been an error in signing up.";
            Controller::redirectToResult($msg, "error");
        }
    }
    
    public function processSignOut() 
    {
        Controller::redirectIfNotAuth();
        Cookie::clearSessionCookie();
        Controller::redirectToResult("Signed out successfully!", "success");
    }

    public function processForgotPwd() 
    {
        //Controller::redirectIfAuth();
        
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);

        $code = VerificationCode::generateCode($email);
        $message = HTMLMessages::getForgottenPassword($code);
        Mailer::send($email, $message);
        Controller::redirectToResult("Sent the code to your email", "success");
    }

    public function processUpdatePwd() 
    {
        Controller::redirectIfAuth();

        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
        $code = filter_input(INPUT_POST, "code", FILTER_SANITIZE_SPECIAL_CHARS);
        $newPwd = filter_input(INPUT_POST, "pwd", FILTER_SANITIZE_EMAIL);
        
        if (VerificationCode::verifyCode($email, $code) === true) {
            $hashNewPwd = password_hash($newPwd, PASSWORD_DEFAULT);
            $this->userRepository->updatePwd($hashNewPwd, $email);
            Controller::redirectToResult("Updated your password!", "success");
        }

        else {
            Controller::redirectToResult("Verification has failed", "error");
        }
    }
}
