<?php

use App\DI\Container;

require_once '../../../vendor/autoload.php';
$config = require_once __DIR__ . '/../../DI/config.php';

$container = new Container();

foreach ($config['services'] as $key => $service) {
    $container->add($key, $service);
}

$db = $container->get('DatabaseAR');
$converter = $container->get('UrlConverter');

$urlString = "https://laravel.su";
//$urlString = "https://www.google.com.ua";
//$urlString = "https://www.adidas.ua";
//$codeString = "6.9WYEG";
$codeString = "aV.H1rs";

echo $urlString . "<br>";
echo $converter->encode($urlString);
echo "<br>------------------------<br>";

echo $codeString . "<br>";
echo $converter->decode($codeString);