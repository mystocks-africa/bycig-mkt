<?php
namespace App\Controllers;
include_once "Controller.php";

use App\Controller;

class AuthController extends Controller
{
    public function signIn()
    {
        parent::redirectAuth();
        parent::render('signIn');
    }

    public function signUp()
    {
        parent::redirectNotAuth();
        parent::render('signUp');
    }

    public function signOut() 
    {
        parent::redirectNotAuth();
        parent::render('signOut');
    }
}