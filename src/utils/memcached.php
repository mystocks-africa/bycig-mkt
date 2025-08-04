<?php
$memcached = new Memcached();
$memcached->addServer('/tmp/memcached.sock', 777);
echo "Memcached server added successfully.";

exit();