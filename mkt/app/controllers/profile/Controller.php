<?php
namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/user/Repository.php";
include_once __DIR__ . "/../../models/holdings/Repository.php";

use App\Core\Controller;
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

    public function deleteUser() 
    {
        $session = Controller::redirectIfNotAuth(returnSession: true);
        $this->userRepository->delete($session['email']);
    }
}