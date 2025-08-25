<?php
namespace App\Core;

use PDO;

class Transaction 
{
    private string $startTransactionQuery = "
        START TRANSACTION;
    ";

    private string $commitQuery = "
        COMMIT;
    ";

    private string $rollbackQuery = "
        ROLLBACK;
    ";

    public function startTransaction(PDO $pdo): void
    {
        $stmt = $pdo->prepare($this->startTransactionQuery);
        $stmt->execute();
    }

    public function commit(PDO $pdo): void 
    {
        $stmt = $pdo->prepare($this->commitQuery);
        $stmt->execute();
    }

    public function rollback(PDO $pdo): void
    {
        $stmt = $pdo->prepare($this->rollbackQuery);
        $stmt->execute();
    }
}