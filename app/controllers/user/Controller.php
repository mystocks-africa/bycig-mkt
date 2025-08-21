<?php
namespace App\Controllers;

include_once __DIR__ . "/../../core/Controller.php";

use App\Core\Controller;

class UserController {
    public function profile() {
        Controller::render("user/profile");
    }
}