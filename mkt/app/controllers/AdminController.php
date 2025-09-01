<?php
namespace App\Controllers;

use App\Core\Controller\Controller;
use App\Core\Auth\Session;
use App\Core\Auth\AuthGuard;

use App\Services\AdminService;
use Exception;

class AdminController 
{
    private Session $session;
    private AuthGuard $authGuard;
    private AdminService $adminService;

    public function __construct() {
        $this->session = new Session();
        $this->authGuard = new AuthGuard($this->session);
        $this->adminService = new AdminService();
    }

    public function index(): void
    {
        $this->authGuard->redirectIfNotAuth();
        $proposals = $this->adminService->getProposalsByClusterLeader($this->session->getSession()['email']);
        Controller::render("admin/index", [
            'proposals' => $proposals
        ]);
    }

    public function processProposalStatusPost(): void 
    {
        $this->authGuard->redirectIfNotAuth();

        try {
            $postId = filter_input(INPUT_GET, 'post_id', FILTER_SANITIZE_NUMBER_INT);
            $clusterLeaderEmail = filter_input(INPUT_GET, 'cluster_leader_email', FILTER_SANITIZE_SPECIAL_CHARS);
            $status = filter_input(INPUT_GET, 'status', FILTER_SANITIZE_SPECIAL_CHARS);

            $this->adminService->processProposalDecision($postId, $clusterLeaderEmail, $status);

            Controller::redirectToResult('Posted proposal successfully', 'success');
        } catch (Exception $error) {
            Controller::redirectToResult($error->getMessage(), 'error');
        }
    }
}