<?php
namespace App\Core\Templates;

require_once __DIR__ . "/../../../vendor/autoload.php";
include_once __DIR__ . "/../../../utils/env.php";

use Predis\Client;

class RedisTemplate {
    protected static function getRedis() {
        global $env;

        $redis = new Client([
                    'scheme' => $env["REDIS_SCHEME"] ?? 'tcp',
                    'host'   => $env["REDIS_HOST"] ?? 'redis', 
                    'port'   => $env["REDIS_PORT"] ?? 6379,
                ]);

        return $redis;
    }
}
