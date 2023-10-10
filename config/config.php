<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

return [
    // Configure Twig
    Environment::class => function () {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        return new Environment($loader);
    },

    \LgrDev\Tools::class => DI\create()->constructor(),

    /* uncomment the following lines if you use PDO */
    /*
    \LgrDev\Storage\PdoDatabase::class => DI\create()->constructor(
                                DI\get('db.type'),
                                DI\get('db.host'),
                                DI\get('db.name'),
                                DI\get('db.user'),
                                DI\get('db.password'),
                                DI\get(\LgrDev\Tools::class)
                            ),
    */    
                         
    /* uncomment the following lines if you use Memcache */
    /*
    \LgrDev\Storage\MemcacheDatabase::class => DI\create()->constructor(
                                DI\get('mc.host'),
                                DI\get('mc.port'),
                                DI\get(\LgrDev\Tools::class)
                            ),
    */    

    \LgrDev\Storage\RedisDatabase::class => DI\create()->constructor(
                                    DI\get('redis.host'),
                                    DI\get('redis.port'),
                                    DI\get('redis.auth'),
                                    DI\get(\LgrDev\Tools::class)
                                ),
 
    \LgrDev\PrivateMessage::class => DI\create()->constructor(
        DI\get(\LgrDev\Storage\RedisDatabase::class),
        DI\get(\LgrDev\Tools::class),
        DI\get(Environment::class)
    ),
];
