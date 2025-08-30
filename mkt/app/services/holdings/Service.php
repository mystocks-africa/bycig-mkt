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

    private function getStockPrice(string $stockSymbol): float 
    {
        try {
            $config = Configuration::getDefaultConfiguration()->setApiKey('token', $this->env['FINNHUB_API_KEY']);
            $client = new DefaultApi(
                new GuzzleClient(),
                $config
            );
            $response = $client->quote($stockSymbol);

            return $response['c']; // c is current price of stock
        } catch (Exception $error) {
            throw $error;
        }
    }

    // We need a try-catch block here so that the ACID transaction rollbacks (reverts) gracefully
    public function processBuyOrder(int $id, string $email): void
    {
        $this->db->getPdo()->beginTransaction();
         
        try {
            $holding = $this->holdingRepository->findById($id);

            if ($holding['fulfilled']) {
                throw new Exception("Order already has been fulfilled.");
            }

            $user = $this->userRepository->findByEmail($email);

            if ($holding['investor'] != $user['email']) {
                throw new Exception("This holding does not belong to you");
            }

            $stockPrice = $this->getStockPrice($holding['stock_ticker']) * $holding['shares'];

            if ($user['balance'] < $stockPrice) {
                throw new Exception("You cannot afford this stock. Invest into others to earn money!");
            }

            $newBalance = $user['balance'] - $stockPrice;
            $this->userRepository->updateBalance($newBalance, $email);

            $this->userRepository->updateBalance($newBalance, $email);
            $this->holdingRepository->fulfillOrder($id);

            $this->db->getPdo()->commit();
        } catch(Exception $error) {
            $this->db->getPdo()->rollBack();
            throw new Exception($error);
        }
    }

    public function processSellOrder(int $id, string $email): void
    {
        $this->db->getPdo()->beginTransaction();
         
        try {
            $holding = $this->holdingRepository->findById($id);

            if (!$holding['fulfilled']) {
                throw new Exception("Holding is not yet fulfilled. You need to buy it before selling it.");
            }

            $user = $this->userRepository->findByEmail($email);

            if ($holding['investor'] != $user['email']) {
                throw new Exception("This holding does not belong to you.");
            }

            $stockPrice = $this->getStockPrice($holding['stock_ticker']) * $holding['shares'];
            $newBalance = $user['balance'] + $stockPrice;
            
            $this->userRepository->updateBalance($newBalance, $email);
            $this->holdingRepository->delete($id); 

            $this->db->getPdo()->commit();
        } catch(Exception $error) {
            $this->db->getPdo()->rollBack();
            throw new Exception($error);
        }
    }
}