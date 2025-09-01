<?php 
namespace App\Controllers;

use App\Core\Controller\Controller;

use App\Services\AccountService;
use Exception;

class AccountController 
{
    private AccountService $accountService;

    public function __construct()
    {
        $this->accountService = new AccountService();
    }

    public function index(): void 
    {
        try {
            $email = filter_input(INPUT_GET, "email", FILTER_SANITIZE_SPECIAL_CHARS);
            $userInfo = $this->accountService->getUserInfo($email);
            Controller::render('account/index', $userInfo);
        } catch (Exception $error) {
            Controller::redirectToResult($error->getMessage(), "error");
        }
    }
}