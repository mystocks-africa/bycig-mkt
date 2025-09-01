<?php
namespace App\Controllers;

use App\Core\Controller\Controller;
use App\Core\Auth\Session;
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

    public function index(): void 
    {
        $this->authGuard->redirectIfNotAuth();

        try {
            $activeTab = filter_input(INPUT_GET, "tab", FILTER_SANITIZE_SPECIAL_CHARS);
            $data = $this->profileService->getProfileData($this->session->getSession()['email'], $activeTab);
            Controller::render('profile/index', $data);
        } catch (Exception $error) {
            Controller::redirectToResult($error->getMessage(), "error");
        }
    }

    public function deleteUser(): void 
    {
        $this->authGuard->redirectIfNotAuth();
        try {
            $this->profileService->deleteProfile($this->session->getSession()['email'], $this->session);
            Controller::redirectToResult("Successfully deleted user", "success");
        } catch (Exception $error) {
            Controller::redirectToResult($error->getMessage(), "error");
        }
    }

    public function updateUser(): void
    {
        $this->authGuard->redirectIfNotAuth();

        try {
            // Get and store all associated data into an array
            $fullName = filter_input(INPUT_POST, "full_name", FILTER_SANITIZE_SPECIAL_CHARS);
            $pwd = filter_input(INPUT_POST, "pwd", FILTER_SANITIZE_SPECIAL_CHARS);
            $clusterLeader = filter_input(INPUT_POST, "cluster_leader", FILTER_SANITIZE_SPECIAL_CHARS);
            $this->profileService->updateAffectedUserFields(
                $this->session->getSession()['email'], 
                $fullName, 
                $clusterLeader,
                $pwd
            );
            Controller::redirectToResult("Updated user data", "success");
        } catch (Exception $error) {
            Controller::redirectToResult($error->getMessage(), "error");
        }
    }
}