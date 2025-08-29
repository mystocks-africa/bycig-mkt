<?php
namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";
include_once __DIR__ . "/../../core/auth/Guard.php";
include_once __DIR__ . "/../../core/auth/Session.php";

include_once __DIR__ . "/../../services/profile/Service.php";

use App\Core\Controller;
use App\Core\Session;
use App\Core\Auth\AuthGuard;

use Exception;
use App\Services\ProfileService;

class ProfileController 
{
    private Session $session;
    private AuthGuard $authGuard;
    private ProfileService $profileService;

    public function __construct() {
        $this->session = new Session();
        $this->authGuard = new AuthGuard($this->session);
        $this->profileService = new ProfileService();
    }

    public function index() 
    {
        $this->authGuard->redirectIfNotAuth();

        try {
            $activeTab = filter_input(INPUT_GET, "tab", FILTER_SANITIZE_SPECIAL_CHARS);
            $data = $this->profileService->getProfileData($this->session->getSession()['email'], $activeTab);
            Controller::render('profile/index', $data);
        } catch (Exception $error) {
            Controller::redirectToResult($error, "error");
        }
    }

    public function deleteUser() 
    {
        $this->authGuard->redirectIfNotAuth();
        try {
            $this->profileService->deleteProfile($this->session->getSession()['email'], $this->session);
            Controller::redirectToResult("Successfully deleted user", "success");
        } catch (Exception $error) {
            Controller::redirectToResult($error, "error");
        }
    }

    public function updateUser(): void
    {
        $this->authGuard->redirectIfNotAuth();

        try {
            // Get and store all associated data into an array
            $fullName = filter_input(INPUT_POST, "full_name", FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_SPECIAL_CHARS);
            $clusterLeader = filter_input(INPUT_POST, "cluster_leader", FILTER_SANITIZE_SPECIAL_CHARS);
            $this->profileService->updateAffectedUserFields($email, $this->session->getSession()['email'], $fullName, $clusterLeader);
            Controller::redirectToResult("Updated user data", "success");
        } catch (Exception $e) {
            Controller::redirectToResult("Error in updating user data", "error");
        }
    }
}