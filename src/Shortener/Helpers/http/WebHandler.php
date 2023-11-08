<?php

namespace App\Shortener\Helpers\http;

use App\Shortener\Service\UrlConverter;
use Exception;
class WebHandler
{
    protected object $urlConverter;

    public function __construct(UrlConverter $urlConverter)
    {
        $this->urlConverter = $urlConverter;
    }

    public function handle(string $uri)
    {
        $uri = $this->uriTransform($uri);
        list($action, $data) = $uri;
        if($this->actionValdation($action)){
            return call_user_func(array($this->urlConverter, $action), $data);
        }
    }

    protected function uriTransform(string $uri): array
    {
        $uri = substr($uri, 1);
        return explode('/', $uri, 2);
    }

    protected function actionValdation(string $action)
    {
        if (method_exists($this->urlConverter, $action)) {
            return true;
        } else {
            throw new Exception('Такого метода нету:');
        }
    }
}