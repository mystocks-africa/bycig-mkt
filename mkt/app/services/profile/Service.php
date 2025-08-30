<?php
namespace App\Services;

include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../core/auth/Session.php";
include_once __DIR__ . "/../../core/auth/Cookie.php";

include_once __DIR__ . "/../../models/user/Repository.php";
include_once __DIR__ . "/../../models/proposals/Repository.php";

use App\DbTemplate;
use App\Core\Session;
use App\Core\Cookie;

use App\Models\Repository\UserRepository;
use App\Models\Repository\HoldingRepository;
use Exception;

class ProfileService
{
    private DbTemplate $db;
    private UserRepository $userRepository;
    private HoldingRepository $holdingRepository;

    public function __construct()
    {
        $this->db = new DbTemplate();
        $this->userRepository = new UserRepository($this->db->getPdo());
        $this->holdingRepository = new HoldingRepository($this->db->getPdo());
    }

    public function getProfileData(string $email, ?string $activeTab = null): array
    {
        // Supported tabs that do not need specialized logic 
        $otherSupportedTabs = [
            "delete-user"
        ];

        if (empty($activeTab) || $activeTab === "info") {
            $user = $this->userRepository->findByEmail($email);
            
            if ($user["role"] === "cluster_leader") {
                $clusterLeaders = null;
            } else {
                $clusterLeaders = $this->userRepository->findAllClusterLeaders();
            } 
            
            return [
                "user" => $user,
                "clusterLeaders" => $clusterLeaders
            ];
        } elseif ($activeTab === "holdings") {
            $holdings = $this->holdingRepository->findByEmail($email);
            
            return [
                "holdings" => $holdings,
            ];
        } elseif (in_array($activeTab, $otherSupportedTabs)) {
            return [];
        } else {
            throw new Exception("Incorrect tab given.");
        }
    }

    public function deleteProfile(string $email, Session $session): void
    {
        try {
            $this->db->getPdo()->beginTransaction();
            $this->userRepository->delete($email);
            $this->holdingRepository->deleteAllHoldings($email);
            $session->deleteSession();
            Cookie::clearSessionCookie();
            if ($this->db->getPdo()->inTransaction()) {
                $this->db->getPdo()->commit();
            }
        } catch (Exception $error) {
            if ($this->db->getPdo()->inTransaction()) {
                $this->db->getPdo()->rollBack();
            }
            throw $error;
        }
    }

    public function updateAffectedUserFields(
        ?string $email,
        string $sessionEmail,
        ?string $fullName,
        ?string $clusterLeader,
        Session $session
    ): void {
        $data = [
            "full_name"      => $fullName,
            "email"          => $email,
            "cluster_leader" => $clusterLeader
        ];

        $fields = [];
        $params = [];

        foreach ($data as $field => $value) {
            if ($value === null) continue;
            // Building SQL-code
            $fields[] = "$field = :$field";
            $params[$field] = $value;
        }

        if (array_key_exists("email", $params)) {
            $session->updateSessionEmail($params['email']);
        }

        if (empty($fields)) {
            throw new Exception("Nothing to update");
        }

        $this->userRepository->update($sessionEmail, $fields, $params);
    }
}