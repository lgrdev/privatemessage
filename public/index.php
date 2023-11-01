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

// add route to account pages
$myrouter->addGet('/signin', $container->get(LgrDev\PrivateMessage::class)->displayAccountSignin());
$myrouter->addGet('/signup', $container->get(LgrDev\PrivateMessage::class)->displayAccountSignup());
$myrouter->addGet('/account', $container->get(LgrDev\PrivateMessage::class)->displayAccount());

// add route to apis
$myrouter->addGet('/api/v1/status/{id:[a-z0-9]+}',  $container->get(LgrDev\Api::class)->apiStatusMessage());
$myrouter->addPost('/api/v1/message', $container->get(LgrDev\Api::class)->apiCreateMessage());
$myrouter->addDelete('/api/v1/message/{id:[a-z0-9]+}', $container->get(LgrDev\Api::class)->apiDeleteMessage());

// display page
$myrouter->run($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);








