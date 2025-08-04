<?php
namespace App\Controllers;
include_once "Controller.php";

use App\Controller;

class HomeController extends Controller
{
    public function index()
    {
        parent::redirectNotAuth();
        parent::render('index');
    }

    public function favicon()
    {
        parent::redirectNotAuth();
        parent::render('favicon');
    }
}