<?php

namespace App\Controllers;
include_once __DIR__ . "/../Controller.php";

use App\Controller;

class HoldingsController extends Controller
{
    public function details() 
    {
        parent::render("/holdings/details");
    }
}