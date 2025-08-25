<?php
namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/user/Repository.php";
include_once __DIR__ . "/../../models/holdings/Repository.php";

use App\Core\Controller;
use App\Models\Entity\UserEntity;
use App\Models\Repository\UserRepository;
use App\Models\Repository\HoldingRepository;

class ProfileController 
{
    private UserRepository $userRepository;
    private HoldingRepository $holdingRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->holdingRepository = new HoldingRepository();
    }

    public function index() 
    {
        $session = Controller::redirectIfNotAuth(returnSession:true);

        $activeTab = filter_input(INPUT_GET, "tab", FILTER_SANITIZE_SPECIAL_CHARS);

        // Supported tabs that do not need specialized logic 
        $otherSupportedTabs = [
            "delete-user"
        ];

        if (empty($activeTab) || $activeTab === "info") {
            $user = $this->userRepository->findByEmail($session["email"]);
            
            if ($user["role"] === "cluster_leader") {
                $clusterLeaders = null;
            } else {
                $clusterLeaders = $this->userRepository->findAllClusterLeaders();
            } 
            
            Controller::render("profile/index", [
                "user"=>$user,
                "clusterLeaders"=>$clusterLeaders
            ]);
        } 

        else if ($activeTab === "holdings") {
            $holdings = $this->holdingRepository->findByEmail($session["email"]);
            
            Controller::render("profile/index", [
                "holdings"=>$holdings,
            ]);
        } 
        
        else if (in_array($activeTab, $otherSupportedTabs)) {
            Controller::render("profile/index");
        }

        else {
            Controller::redirectToResult("Unsupported tab given", "error");
        }
    }

    // IMPLEMENT ACID TRANSCATION FOR FAIL-SAFE DELETION
    public function deleteUser() 
    {
        $session = Controller::redirectIfNotAuth(returnSession: true);

        try {
            $this->userRepository->delete($session['email']);
            $deleted = true;

            if ($deleted) {
                try {
                    $this->holdingRepository->deleteAllHoldings($session['email']);
                    Controller::redirectToResult("User and holdings deleted successfully.", "success");
                } catch (\Exception $e) {
                    // Re-add user if holdings deletion fails to avoid problems
                    $userEntity = new UserEntity(
                        $session['email']
                    );
                    $this->userRepository->save($session['email']);
                    Controller::redirectToResult("Failed to delete holdings, both user and holdings were not deleted" . $e->getMessage() . ". User has been re-added.", "warning");
                }
            } else {
                Controller::redirectToResult("Failed to delete user, both user and holdings were not deleted.", "error");
            }
        } catch (\Exception $e) {
            Controller::redirectToResult("Failed to delete user: " . $e->getMessage(), "error");
        }
    }
}