<?php 
namespace App\Controllers;

include_once __DIR__ . "/../../core/controller/Controller.php";

include_once __DIR__ . "/../../services/account/Service.php";

use App\Core\Controller;

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
            Controller::render('account/index', [
                "holdings" => $userInfo["holdings"],
                "user" => $userInfo["user"]
            ]);
        } catch (Exception $error) {
            Controller::redirectToResult($error->getMessage(), "error");
        }
    }
}