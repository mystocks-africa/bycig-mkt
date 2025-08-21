<?php
namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";

use App\Core\Controller;


class ProfileController 
{
    public function index() 
    {
        Controller::redirectIfNotAuth();
        Controller::render("profile/index");
    }
}