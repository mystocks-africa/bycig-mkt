<?php

namespace App\Controllers;
include_once __DIR__ . "/Controller.php";

use App\Controller;

class AdminController extends Controller 
{
    public function index()
    {
        parent::render("admin/index");
    }
}