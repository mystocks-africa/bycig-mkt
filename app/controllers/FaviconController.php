<?php
namespace App\Controllers;
include_once "Controller.php";

use App\Controller;

class FaviconController extends Controller 
{
    public function favicon()
    {
        parent::render('favicon');
    }
}