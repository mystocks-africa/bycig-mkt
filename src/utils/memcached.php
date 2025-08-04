<?php
$memcached = new Memcached();
$memcached->addServer('/tmp/memcached.sock', 777);
exit();