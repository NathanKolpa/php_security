<?php


namespace core\handling;

use core\http\HttpRequest;

require_once "core/http/HttpRequest.php";

class Request extends HttpRequest
{
    private $routeData;
    private $middlewareData;

    public function __construct(array $routeData, array $middlewareData,string $method, string $target, string $body)
    {
        parent::__construct($method, $target, $body);
        $this->routeData = $routeData;
        $this->middlewareData = $middlewareData;
    }
    
    public function getRouteData(string $name) 
    {
        return $this->routeData[$name];
    }

    public function getMiddlewareData(string $name)
    {
        return $this->middlewareData[$name];
    }
    
    public function addMiddlewareData(string $name, $value)
    {
        $this->routeData[$name] = $value;
    }
}