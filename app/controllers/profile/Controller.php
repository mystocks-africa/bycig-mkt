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

        $user = $this->userRepository->findByEmail($session["email"]);

        if ($user["role"] === "cluster_leader") {
            $clusterLeaders = null;
        } else {
            $clusterLeaders = $this->userRepository->findAllClusterLeaders();
        } 

        $holdings = $this->holdingRepository->findAll();

        Controller::render("profile/index", [
            "user"=>$user,
            "clusterLeaders"=>$clusterLeaders,
            "holdings"=>$holdings
        ]);
    }
}