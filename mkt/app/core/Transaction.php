<?php
namespace App\Core;

use PDO;

class Transaction 
{
    private PDO $pdo;

    private string $startTransactionQuery = "
        START TRANSACTION;
    ";

    private string $commitQuery = "
        COMMIT;
    ";

    private string $rollbackQuery = "
        ROLLBACK;
    ";

    public function __construct(PDO $pdo) 
    {
        $this->pdo = $pdo;
    }

    public function startTransaction(): void
    {
        $stmt = $this->pdo->prepare($this->startTransactionQuery);
        $stmt->execute();
    }

    public function commit(): void 
    {
        $stmt = $this->pdo->prepare($this->commitQuery);
        $stmt->execute();
    }

    public function rollback(): void
    {
        $stmt = $this->pdo->prepare($this->rollbackQuery);
        $stmt->execute();
    }
}