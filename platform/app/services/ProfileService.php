<?php
namespace App\Services;

use App\Core\Templates\DbTemplate;
use App\Core\Auth\Session;
use App\Core\Auth\Cookie;

use App\Models\Holdings\Repository as HoldingRepository;
use App\Models\User\Repository as UserRepository;
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
        } else if ($activeTab === "holdings") {
            $holdings = $this->holdingRepository->findByEmail($email);
            
            return [
                "holdings" => $holdings
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
        string $email,
        ?string $fullName,
        ?string $clusterLeader,
        ?string $pwd
    ): void {
        $data = [
            "full_name"      => $fullName,
            "cluster_leader" => $clusterLeader,
            "pwd"            => password_hash($pwd, PASSWORD_DEFAULT)
        ];

        $fields = [];
        $params = [];

        foreach ($data as $field => $value) {
            if ($value === null) continue;
            // Building SQL-code
            $fields[] = "$field = :$field";
            $params[$field] = $value;
        }


        if (empty($fields)) {
            throw new Exception("Nothing to update");
        }

        $this->userRepository->update($email, $fields, $params);
    }
}