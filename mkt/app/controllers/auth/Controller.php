<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";
include_once __DIR__ . "/../../core/auth/Session.php";
include_once __DIR__ . "/../../core/auth/Guard.php";
include_once __DIR__ . "/../../services/auth/Service.php";

use App\Core\Controller;
use App\Core\Session;
use App\Core\Auth\AuthGuard;
use App\Services\Auth\AuthService;
use Exception;

class AuthController
{
    private Session $session;
    private AuthGuard $authGuard;
    private AuthService $authService;

    public function __construct() {
        $this->session = new Session();
        $this->authGuard = new AuthGuard($this->session);
        $this->authService = new AuthService();
    }

    public function signIn(): void {
        $this->authGuard->redirectIfAuth();
        Controller::render('/auth/signin');
    }

    public function signUp(): void {
        $this->authGuard->redirectIfAuth();
        try {
            $clusterLeaderEmails = $this->authService->getClusterLeaderEmails();
            Controller::render('/auth/signup', ['clusterLeaderEmails' => $clusterLeaderEmails]);
        } catch (Exception $e) {
            Controller::redirectToResult("Error loading signup page", "error");
        }
    }

    public function forgotPwd(): void {
        Controller::render("auth/forgot-pwd");
    }

    public function updatePwd(): void {
        $code = filter_input(INPUT_GET, "code", FILTER_SANITIZE_SPECIAL_CHARS);
        Controller::render("auth/update-pwd", ["code" => $code]);
    }

    public function processSignIn(): void {
        $this->authGuard->redirectIfAuth();
        try {
            $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
            $pwd   = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS));
            $this->authService->validateUserAccount($email, $pwd, $this->session);
            Controller::redirectToResult("Successfully logged in", "success");
        } catch (Exception $e) {
            Controller::redirectToResult("Invalid email or password", "error");
        }
    }

    public function processSignUp(): void {
        $this->authGuard->redirectIfAuth();
        try {
            $email        = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $pwd          = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
            $clusterLeader= filter_input(INPUT_POST, 'cluster_leader', FILTER_SANITIZE_SPECIAL_CHARS);
            $fullName     = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_SPECIAL_CHARS);
            $this->authService->createUserAccount($email, $pwd, $clusterLeader, $fullName);
            Controller::redirectToResult("User has been created. You may sign in now.", "success");
        } catch (Exception $e) {
            Controller::redirectToResult("There has been an error in signing up.", "error");
        }
    }

    public function processSignOut(): void {
        $this->authGuard->redirectIfNotAuth();
        try {
            $this->authService->deleteSession($this->session);
            Controller::redirectToResult("Signed out successfully!", "success");
        } catch (Exception $e) {
            Controller::redirectToResult("There was a problem signing out. Try again.", "error");
        }
    }

    public function processForgotPwd(): void {
        try {
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
            $this->authService->sendForgotPwdCode($email);
            Controller::redirectToResult("Sent the code to your email", "success");
        } catch (Exception $e) {
            Controller::redirectToResult("Failed to send the code. Try again.", "error");
        }
    }

    public function processUpdatePwd(): void {
        $this->authGuard->redirectIfAuth();
        try {
            $email  = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
            $code   = filter_input(INPUT_POST, "code", FILTER_SANITIZE_SPECIAL_CHARS);
            $newPwd = filter_input(INPUT_POST, "pwd", FILTER_SANITIZE_SPECIAL_CHARS);
            $this->authService->validateForgotPwdCode($email, $code, $newPwd);
            Controller::redirectToResult("Updated your password successfully", "success");
        } catch (Exception $e) {
            Controller::redirectToResult("Failed to update password", "error");
        }
    }
}