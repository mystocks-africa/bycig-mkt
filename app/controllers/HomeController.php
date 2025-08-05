<?php

namespace App\Controllers;
include_once "Controller.php";

use App\Controller;

class HomeController extends Controller
{
    public function index()
    {
        parent::redirectIfNotAuth();
        parent::render('index');
    }

    public function favicon()
    {
        parent::redirectIfNotAuth();
        parent::render('favicon');
    }

    public function redirect() {
        parent::render('redirect');
    }
}