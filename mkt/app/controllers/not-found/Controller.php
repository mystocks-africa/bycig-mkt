<?php
namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";

use App\Core\Controller\Controller;

class NotFoundController
{
    public function index(): void 
    {
        Controller::render("not-found");
    }
}