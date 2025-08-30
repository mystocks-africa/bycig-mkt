<?php
namespace App\Services;

include_once __DIR__ . "/../../core/templates/DbTemplate.php";
include_once __DIR__ . "/../../core/files/Files.php";

include_once __DIR__ . "/../../models/holdings/Repository.php";
include_once __DIR__ . "/../../models/user/Repository.php";
include_once __DIR__ . "/../../../utils/env.php";

use App\DbTemplate;
use App\Core\Files;

use App\Models\Repository\HoldingRepository;
use App\Models\Repository\UserRepository;
use Exception;
use Finnhub\Api\DefaultApi;
use Finnhub\Configuration;
use GuzzleHttp\Client as GuzzleClient;

class HoldingService 
{
    private DbTemplate $db;
    private HoldingRepository $holdingRepository;
    private UserRepository $userRepository;
    private $env;

    public function __construct()
    {
        global $env; // get the env from env.php helper file thru global

        $this->env = $env;
        $this->db = new DbTemplate();
        $this->holdingRepository = new HoldingRepository($this->db->getPdo());
        $this->userRepository = new UserRepository($this->db->getPdo());
    }

    // We need a try-catch block here so that the ACID transaction rollbacks (reverts) gracefully
    public function processBuyOrder(int $id, string $email): void
    {
        $this->db->getPdo()->beginTransaction();
         
        try {
            $holding = $this->holdingRepository->findById($id);

            if (!$holding['fulfilled']) {
                throw new Exception("Order already has been fulfilled.");
            }

            $user = $this->userRepository->findByEmail($email);

            $config = Configuration::getDefaultConfiguration()->setApiKey('token', $this->env['FINNHUB_API_KEY']);
            $client = new DefaultApi(
                new GuzzleClient(),
                $config
            );
            $response = $client->quote($holding['stock_symbol']);
            $newBalance = $user['balance'] - $response->c; // current price of stock

            if ($newBalance < 0) {
                throw new Exception("You cannot afford this stock. Invest into others to earn money!");
            }

            $this->userRepository->updateBalance($newBalance, $email);
            $this->holdingRepository->fulfillOrder($id);
            Files::deleteFile($holding['proposal_file']);

            $this->db->getPdo()->commit();
        } catch(Exception $error) {
            $this->db->getPdo()->rollBack();
        }
    }
}