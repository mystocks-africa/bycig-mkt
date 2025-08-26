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

    public function deleteUser() 
    {
        $session = Controller::redirectIfNotAuth(returnSession: true);

        // Implemented ACID transactions to ensure fail-safe deletion
        $transaction = new Transaction($this->db->getPdo());
        $transaction->startTransaction();

        try {
            $this->userRepository->delete($session['email'], $this->db->getPdo());
            $this->holdingRepository->deleteAllHoldings($session['email'], $this->db->getPdo());
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollback();
            Controller::redirectToResult("Failed to delete user: " . $e->getMessage(), "error");
        }
    }
}