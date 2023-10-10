<?php

return [
    // uncomment the folowing lines if you use PDO
    // 'db.type'       => DI\env('db_type', 'mysql'),
    // 'db.host'       => DI\env('db_host', 'localhost'),
    // 'db.name'       => DI\env('db_name', 'mydatabase'),
    // 'db.user'       => DI\env('db_user', 'myuser'),
    // 'db.password'   => DI\env('db_password', 'mypassword'),

    // uncomment the folowing lines if you use Memcache
    // 'mc.host'    => DI\env('mc_host', 'localhost'),
    // 'mc.port'    => DI\env('mc_port', 11211),

    // if you use Redis
    'redis.host'    => DI\env('redis_host', 'localhost'),
    'redis.port'    => DI\env('redis_port', 6379),
    'redis.auth'    => DI\env('redis_auth', 'mypassword'),

];

?>