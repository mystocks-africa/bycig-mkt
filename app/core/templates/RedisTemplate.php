<?php
namespace App\Core\Templates;

require_once __DIR__ . "/../../../vendor/autoload.php";
include_once __DIR__ . "/../../../utils/env.php";

use Predis\Client;

class RedisTemplate {
    protected static function getRedis() {
        global $env;

        $redis = new Client($env["REDIS_URI"]);

        return $redis;
    }
}
