<?php

namespace App\Controllers;
include_once __DIR__ . "/../../core/controller-helper/Controller.php";

use App\Core\ControllerHelper;

class NotFoundController extends Controller {
    private $controllerHelper;

    public function __construct()
    {
        $this->controllerHelper = new ControllerHelper();
    }

    public function index() 
    {
        parent::render("not-found");
    }
}