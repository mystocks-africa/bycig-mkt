<?php
namespace App\Core\Templates;

require_once __DIR__ . "/../../../vendor/autoload.php";
include_once __DIR__ . "/../../../utils/env.php";

use Predis\Client;

class RedisTemplate 
{

    private Client $redis;

    public function __construct()
    {
        global $env;

        $this->redis = new Client([
                    'scheme' => $env["REDIS_SCHEME"] ?? 'tcp',
                    'host'   => $env["REDIS_HOST"] ?? 'redis', 
                    'port'   => $env["REDIS_PORT"] ?? 6379,
                ]);
    }

    public function getRedis()
    {
        return $this->redis;
    }
}
