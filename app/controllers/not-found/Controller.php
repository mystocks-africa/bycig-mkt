<?php

namespace App\Controllers;
include_once __DIR__ . "/../Controller.php";

use App\Controller;

class NotFoundController extends Controller {
    public function index() 
    {
        parent::render("not-found");
    }
}