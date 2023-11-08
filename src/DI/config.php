<?php

use App\Shortener\Service\UrlConverter;
use App\Shortener\Repository\FileRepository;
use App\Shortener\Helpers\Validation\UrlValidator;
use App\ORM\Model\UrlConverterRepository;
use App\ORM\ActiveRecord\DatabaseAR;
use App\Shortener\Helpers\http\WebHandler;

return [
    'services' => [
        'WebHandler' => function ($container) {
            $converter = $container->get('UrlConverter');

            $webHandler = new WebHandler($converter);

            return $webHandler;
        },
        'DatabaseAR' => function ($container) {
            $database = 'hillel_task';
            $user = 'root';
            $pass = '';
            $host = 'db_mysql';

            $db = new DatabaseAR($database, $user, $pass, $host);

            return $db;
        },
        'UrlConverter' => function ($container) {
            $validator = $container->get('UrlValidator');
            $fileRepository = $container->get('FileRepository');
            $databaseRepository = $container->get('UrlConverterRepository');
            $numberCharCode = 7;
            $codeSalt = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $saveToDatabase = true;  // Переменная $saveToDatabase выступает как флаг, если значение true(запись данных в бд), если значение false(запись данных в файл)

            $converter = new UrlConverter($validator, $fileRepository, $databaseRepository, $numberCharCode, $codeSalt, $saveToDatabase);

            return $converter;
        },
        'FileRepository' => function ($container) {
            $fileName = '../file.txt';

            $fileRepository = new FileRepository($fileName);

            return $fileRepository;
        },
        'UrlValidator' => function ($container) {

            $validator = new UrlValidator();

            return $validator;
        },
        'UrlConverterRepository' => function ($container) {

            $databaseRepository = new UrlConverterRepository();

            return $databaseRepository;
        }
    ]
];