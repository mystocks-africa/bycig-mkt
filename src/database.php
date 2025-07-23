<?php
    use React\MySQL\Factory;

    $factory =  new Factory();
    $env = parse_ini_file('.env');
    $mysql_uri = $env["MYSQL_URI"];
    $mysql = $factory->createLazyConnection($mysql_uri);