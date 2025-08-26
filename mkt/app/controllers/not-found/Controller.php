<?php

namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";

use App\Core\Controller;

class NotFoundController
{
    public function index() 
    {
        Controller::render("not-found");
    }
}