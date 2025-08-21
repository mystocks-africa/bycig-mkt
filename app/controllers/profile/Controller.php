<?php
namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";
include_once __DIR__ . "/../../models/user/Repository.php";

use App\Core\Controller;
use App\Models\Repository\UserRepository;

class ProfileController 
{
    private UserRepository $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function index() 
    {
        $session = Controller::redirectIfNotAuth(returnSession:true);

        $user = $this->userRepository->findByEmail($session["email"]);
        $clusterLeaders = $this->userRepository->findAllClusterLeaders();

        Controller::render("profile/index", [
            "user"=>$user,
            "clusterLeaders"=>$clusterLeaders
        ]);
    }
}