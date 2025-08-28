<?php
namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../core/auth/Session.php";
include_once __DIR__ . "/../../models/user/Repository.php";
include_once __DIR__ . "/../../models/holdings/Repository.php";

use App\Core\Controller;
use App\Core\Cookie;
use App\Core\Session;
use App\DbTemplate;
use App\Models\Repository\UserRepository;
use App\Models\Repository\HoldingRepository;
use Exception;

class ProfileController 
{
    private UserRepository $userRepository;
    private HoldingRepository $holdingRepository;
    private DbTemplate $db;

    public function __construct() {
        $this->db = new DbTemplate();
        $this->userRepository = new UserRepository($this->db->getPdo());
        $this->holdingRepository = new HoldingRepository($this->db->getPdo());
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

    public function deleteUser() 
    {
        $session = Controller::redirectIfNotAuth(returnSession: true);
        // only need session object in one method, so it doesn't make sense to inject it in constructor for the whole controller class
        $sessionObj = new Session();

        // Implemented ACID transactions to ensure fail-safe deletion
        $this->db->getPdo()->beginTransaction();

        try {
            $this->userRepository->delete($session['email']);
            $this->holdingRepository->deleteAllHoldings($session['email']);
            $sessionObj->deleteSession();
            Cookie::clearSessionCookie();
            $this->db->getPdo()->commit();
        } catch (Exception $e) {
            $this->db->getPdo()->rollBack();
            Controller::redirectToResult("Failed to delete user: " . $e->getMessage(), "error");
        }
    }

    public function updateUser(): void
    {
        $session = Controller::redirectIfNotAuth(returnSession: true);

        // Get and store all associated data into an array
        $fullName = filter_input(INPUT_POST, "full_name", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_SPECIAL_CHARS);
        $clusterLeader = filter_input(INPUT_POST, "cluster_leader", FILTER_SANITIZE_SPECIAL_CHARS);
        $data = [
            "full_name" => $fullName,
            "email" => $email,
            "cluster_leader" => $clusterLeader
        ];

        // This will be the data that actually is in need of updation
        $fields = [];

        if (empty($fields)) {
            throw new Exception("Nothing to update");
        }

        foreach ($data as $field => $value) {
            if ($value === null) {
                continue; // skip 
            }

            $fields[] = "$field = :$field";
            $params[$field] = $value;
        }

        try {
            $this->userRepository->update($session['email'], $fields, $params);
            Controller::redirectToResult("Updated user data", "success");
        } catch (Exception $e) {
            Controller::redirectToResult("Error in updating user data", "error");
        }
    }
}