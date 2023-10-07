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

    /* uncomment the folowing lines if you use PDO
    \LgrDev\Database::class => \DI\object()->constructor(
                                    \DI\get('db.type'),
                                    \DI\get('db.host'),
                                    \DI\get('db.name'),
                                    \DI\get('db.user'),
                                    \DI\get('db.password'),
                                ),
    */

    \LgrDev\RedisDatabase::class => DI\create()->constructor(
        DI\get('redis.host'),
        DI\get('redis.port'),
        DI\get('redis.auth'),
        DI\get(\LgrDev\Tools::class)
    ),


    \LgrDev\PrivateMessage::class => DI\create()->constructor(
        DI\get(\LgrDev\RedisDatabase::class),
        DI\get(\LgrDev\Tools::class),
        DI\get(Environment::class)
    ),
];
