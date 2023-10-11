<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

return [
    // Configure Twig
    Environment::class => function () {
        $loader = new FilesystemLoader(__DIR__ . '/../templates');
        return new Environment($loader);
    },

    \LgrDev\Tools::class => DI\create()->constructor(dirname(__FILE__,2).'/.env'),

    /* uncomment the following lines if you use PDO */
    // \LgrDev\Storage\PdoDatabase::class => DI\create()->constructor(DI\get(\LgrDev\Tools::class)),
        
                         
    /* uncomment the following lines if you use Memcache */
    // \LgrDev\Storage\MemcacheDatabase::class => DI\create()->constructor(DI\get(\LgrDev\Tools::class)),

    \LgrDev\Storage\RedisDatabase::class => DI\create()->constructor(DI\get(\LgrDev\Tools::class)),
 
    \LgrDev\PrivateMessage::class => DI\create()->constructor(
        DI\get(\LgrDev\Storage\RedisDatabase::class),
        DI\get(\LgrDev\Tools::class),
        DI\get(Environment::class)
    ),
];
