<?php

namespace App\Core;
include_once __DIR__ . "/../core/templates/MemcachedTemplate.php";
include_once __DIR__ . "/Session.php";

use App\Core\Templates\MemcachedTemplate;
use App\Core\Session;

class VerificationCode extends MemcachedTemplate
{
    public static function generateCode() {
        $memcached = parent::getMemcached();

        $email = Session::getSession()["email"];
        $code = rand(10000,99999);

        $expiration = 5 * 60; // 5 minutes in seconds
        $memcached->set($email, $code, $expiration);

        parent::removeMemcached($memcached);

        return $code;
    }
}