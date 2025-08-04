<?php
namespace App\Controllers;
include_once "Controller.php";

use App\Controller;

class HomeController extends Controller
{
    public function index()
    {

        parent::render('index');
    }
}