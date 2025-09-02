<?php
namespace App\Services;

use App\Core\Templates\DbTemplate;

use App\Models\Holdings\Repository as HoldingRepository;
use App\Models\User\Repository as UserRepository;
use Finnhub\Api\DefaultApi;
use Finnhub\Configuration;
use GuzzleHttp\Client as GuzzleClient;
use Exception;
use Dotenv\Dotenv;

class HoldingService 
{
    private DbTemplate $db;
    private HoldingRepository $holdingRepository;
    private UserRepository $userRepository;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../");
        $dotenv->load();

        $this->db = new DbTemplate();
        $this->holdingRepository = new HoldingRepository($this->db->getMysqli());
        $this->userRepository = new UserRepository($this->db->getMysqli());
    }

    private function getStockPrice(string $stockSymbol): float 
    {
        try {
            $config = Configuration::getDefaultConfiguration()->setApiKey('token', $_ENV["FINNHUB_API_KEY"]);
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
        try {        
            // Start transaction using MySQLi
            $this->db->getMysqli()->begin_transaction();

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
            $this->holdingRepository->fulfillOrder($id);

            // Commit transaction using MySQLi
            if ($this->db->getMysqli()->connect_errno === 0) {
                $this->db->getMysqli()->commit();
            }        
        } catch(Exception $error) {
            // Rollback transaction using MySQLi
            $this->db->getMysqli()->rollback();            
            throw $error;
        }
    }

    public function processSellOrder(int $id, string $email): void
    {         
        try {
            // Start transaction using MySQLi
            $this->db->getMysqli()->begin_transaction();

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

            // Commit transaction using MySQLi
            if ($this->db->getMysqli()->connect_errno === 0) {
                $this->db->getMysqli()->commit();
            }
        } catch(Exception $error) {
            // Rollback transaction using MySQLi
            $this->db->getMysqli()->rollback();            
            throw $error;
        }
    }
}