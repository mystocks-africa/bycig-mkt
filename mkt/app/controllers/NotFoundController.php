<?php
namespace App\Controllers;

use App\Core\Controller\Controller;

class NotFoundController
{
    public function index(): void 
    {
        Controller::render("not-found");
    }
}