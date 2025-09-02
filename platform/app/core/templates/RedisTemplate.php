<?php
namespace App\Core\Templates;

use Predis\Client;
use Dotenv\Dotenv;

class RedisTemplate 
{
    private Client $redis;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../../");
        $dotenv->load();

        $this->redis = new Client($_ENV["REDIS_URL"] ?? "redis://redis:6379");
    }

    public function getRedis(): Client
    {
        return $this->redis;
    }
}
