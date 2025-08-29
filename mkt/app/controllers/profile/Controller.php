<?php
namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";
include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../core/auth/Guard.php";
include_once __DIR__ . "/../../core/auth/Session.php";

include_once __DIR__ . "/../../models/user/Repository.php";
include_once __DIR__ . "/../../models/holdings/Repository.php";

use App\Core\Controller;
use App\Core\Cookie;
use App\Core\Session;
use App\DbTemplate;
use App\Core\Auth\AuthGuard;

use App\Models\Repository\UserRepository;
use App\Models\Repository\HoldingRepository;
use Exception;

class ProfileController 
{
    private UserRepository $userRepository;
    private HoldingRepository $holdingRepository;
    private DbTemplate $db;
    private Session $session;

    public function __construct() {
        $this->db = new DbTemplate();
        $this->userRepository = new UserRepository($this->db->getPdo());
        $this->holdingRepository = new HoldingRepository($this->db->getPdo());
        $this->session = new Session();
    }

    public function index() 
    {
        AuthGuard::redirectIfNotAuth($this->session);

        $activeTab = filter_input(INPUT_GET, "tab", FILTER_SANITIZE_SPECIAL_CHARS);

        // Supported tabs that do not need specialized logic 
        $otherSupportedTabs = [
            "delete-user"
        ];

        if (empty($activeTab) || $activeTab === "info") {
            $user = $this->userRepository->findByEmail($this->session->getSession()["email"]);
            
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
            $holdings = $this->holdingRepository->findByEmail($this->session->getSession()["email"]);
            
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
        AuthGuard::redirectIfNotAuth($this->session);

        // Implemented ACID transactions to ensure fail-safe deletion
        $this->db->getPdo()->beginTransaction();

        try {
            $this->userRepository->delete($this->session->getSession()['email']);
            $this->holdingRepository->deleteAllHoldings($this->session->getSession()['email']);
            $this->session->deleteSession();
            Cookie::clearSessionCookie();
            $this->db->getPdo()->commit();
        } catch (Exception $e) {
            $this->db->getPdo()->rollBack();
            Controller::redirectToResult("Failed to delete user: " . $e->getMessage(), "error");
        }
    }

    public function updateUser(): void
    {
        AuthGuard::redirectIfNotAuth($this->session);

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
            $this->userRepository->update($this->session->getSession()['email'], $fields, $params);
            Controller::redirectToResult("Updated user data", "success");
        } catch (Exception $e) {
            Controller::redirectToResult("Error in updating user data", "error");
        }
    }
}