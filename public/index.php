<?php
use \DI\ContainerBuilder;

require_once '../vendor/autoload.php';

session_start();

$containerBuilder = new ContainerBuilder();

$containerBuilder->useAutowiring(true);
$containerBuilder->addDefinitions("../config/config.prod.php");
$containerBuilder->addDefinitions('../config/config.php');
$containerBuilder->enableCompilation('../cache');
$container = $containerBuilder->build();

$container->get(LgrDev\PrivateMessage::class)->index();

