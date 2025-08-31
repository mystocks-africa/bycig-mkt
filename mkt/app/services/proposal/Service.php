<?php
namespace App\Services;

include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../core/files/Files.php";

include_once __DIR__ . "/../../models/proposals/Repository.php";
include_once __DIR__ . "/../../models/proposals/Entity.php";
include_once __DIR__ . "/../../models/user/Repository.php";

use App\DbTemplate;
use App\Core\Files;

use App\Models\Repository\ProposalRepository;
use App\Models\Repository\UserRepository;
use App\Models\Entity\ProposalEntity;
use Exception;

class ProposalService
{
    private DbTemplate $db;
    private ProposalRepository $proposalRepository;
    private UserRepository $userRepository;
    private string $fileName; // fileName is an attribute because the object needs it in another method

    public function __construct()
    {
        $this->db = new DbTemplate();
        $this->proposalRepository = new ProposalRepository($this->db->getPdo());
        $this->userRepository = new UserRepository($this->db->getPdo());
    }

    public function createProposal(
        string $email,
        string $stockTicker,
        string $stockName,
        string $subjectLine,
        string $thesis,
        int $shares,
        array $proposalFile
    ): void {
        $this->fileName = Files::uploadFile($proposalFile);

        if (!$stockTicker || !$stockName || !$subjectLine || !$thesis || !$shares || !$this->fileName) {
            throw new Exception("Error in form input");
        }

        $user = $this->userRepository->findByEmail($email);

        if (!$user["cluster_leader"]) {
            throw new Exception("You need to link with a cluster leader before completing this operation");
        }

        $proposalEntity = new ProposalEntity(
            $email, 
            $stockTicker, 
            $stockName, 
            $subjectLine, 
            $thesis, 
            $shares, 
            $this->fileName
        );

        $this->proposalRepository->save($proposalEntity);
    }

    public function deleteFileOnError(?string $fileName): void
    {
        // Delete the file if there was an error to avoid orphaned files
        // We need to get the file from the server before deletion because sometimes the error happens before creation
        $file = Files::getFile($fileName);
        if (isset($file)) {
            Files::deleteFile($fileName);
        }
    }

    public function getFileName(): string 
    {
        return $this->fileName;
    }
}