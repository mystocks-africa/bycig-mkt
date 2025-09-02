<?php
namespace App\Core\Templates;

use Predis\Client;

class RedisTemplate 
{
    private $env;
    private Client $redis;

    public function __construct()
    {
        global $env;
        $this->env = $env;
        $this->redis = new Client($this->env["REDIS_URL"] ?? "redis://redis:6379");
    }

    public function getRedis(): Client
    {
        return $this->redis;
    }
}
