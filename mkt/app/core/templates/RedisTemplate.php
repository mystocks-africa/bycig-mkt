<?php
namespace App\Core\Templates;

require_once __DIR__ . "/../../../vendor/autoload.php";
include_once __DIR__ . "/../../../utils/env.php";

use Predis\Client;

class RedisTemplate 
{
    private $env;
    private Client $redis;

    public function __construct()
    {
        global $env;
        $this->env = $env;
        $this->redis = new Client($this->env["REDIS_URL"]);
    }

    public function getRedis(): Client
    {
        return $this->redis;
    }
}
