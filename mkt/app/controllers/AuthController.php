<?php
namespace App\Controllers;

use App\Core\Controller\Controller;
use App\Core\Auth\Session;
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
        $this->authGuard->redirectIfAuth();
        Controller::render("auth/forgot-pwd");
    }

    public function updatePwd(): void {
        $this->authGuard->redirectIfAuth();
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
        } catch (Exception $error) {
            Controller::redirectToResult($error->getMessage(), "error");
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
        } catch (Exception $error) {
            Controller::redirectToResult($error->getMessage(), "error");
        }
    }

    public function processSignOut(): void {
        $this->authGuard->redirectIfNotAuth();
        try {
            $this->authService->deleteSession($this->session);
            Controller::redirectToResult("Signed out successfully!", "success");
        } catch (Exception $error) {
            Controller::redirectToResult($error->getMessage(), "error");
        }
    }

    public function processForgotPwd(): void {
        $this->authGuard->redirectIfAuth();
        try {
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
            $this->authService->sendForgotPwdCode($email);
            Controller::redirectToResult("Sent the code to your email", "success");
        } catch (Exception $error) {
            Controller::redirectToResult($error->getMessage(), "error");
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
        } catch (Exception $error) {
            Controller::redirectToResult($error->getMessage(), "error");
        }
    }
}