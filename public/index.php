<?php
use \DI\ContainerBuilder;
use Lgrdev\SimpleRouter\SimpleRouter;

require_once '../vendor/autoload.php';

session_start();

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions('../config/config.php');
// $containerBuilder->useAutowiring(true);
// $containerBuilder->enableCompilation('../cache');
$container = $containerBuilder->build();


// Usage:
$myrouter = new SimpleRouter();

// add route to home page
$myrouter->addGet('/', $container->get(LgrDev\PrivateMessage::class)->displayIndex() );
$myrouter->addGet('/index.php', $container->get(LgrDev\PrivateMessage::class)->displayIndex() );
$myrouter->addGet('/message/{id:[a-z0-9]+}',  $container->get(LgrDev\PrivateMessage::class)->displayMessage());
$myrouter->addPost('/message', $container->get(LgrDev\PrivateMessage::class)->displayResultKey());

// display page
$myrouter->run($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);








