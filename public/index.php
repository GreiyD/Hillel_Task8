<?php

use App\DI\Container;

require_once '../vendor/autoload.php';
$config = require_once __DIR__ . '/../configs/configDI.php';

$container = new Container();

foreach ($config['services'] as $key => $service) {
    $container->add($key, $service);
}

$db = $container->get('DatabaseAR');
$webHandler = $container->get('WebHandler');

echo $webHandler->handle($_SERVER['REQUEST_URI']);