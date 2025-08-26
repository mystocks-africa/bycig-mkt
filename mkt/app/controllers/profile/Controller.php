<?php
namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/user/Repository.php";
include_once __DIR__ . "/../../models/holdings/Repository.php";
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../core/Transaction.php";

use App\Core\Controller;
use App\Core\Transaction;
use App\DbTemplate;
use App\Models\Repository\UserRepository;
use App\Models\Repository\HoldingRepository;

class ProfileController 
{
    private UserRepository $userRepository;
    private HoldingRepository $holdingRepository;
    private DbTemplate $db;

    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->holdingRepository = new HoldingRepository();
        $this->db->getPdo() = new DbTemplate();
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
            $user = $this->userRepository->findByEmail($session["email"], $this->db->getPdo());
            
            if ($user["role"] === "cluster_leader") {
                $clusterLeaders = null;
            } else {
                $clusterLeaders = $this->userRepository->findAllClusterLeaders($this->db->getPdo());
            } 
            
            Controller::render("profile/index", [
                "user"=>$user,
                "clusterLeaders"=>$clusterLeaders
            ]);
        } 

        else if ($activeTab === "holdings") {
            $holdings = $this->holdingRepository->findByEmail($session["email"], $this->db->getPdo());
            
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
        $transaction = new Transaction();
        $transaction->startTransaction($this->db->getPdo());

        try {
            $this->userRepository->delete($session['email'], $this->db->getPdo());
            $deleted = true;

            if ($deleted) {
                try {
                    $this->holdingRepository->deleteAllHoldings($session['email'], $this->db->getPdo());
                    Controller::redirectToResult("User and holdings deleted successfully.", "success");
                } catch (\Exception $e) {
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