<?php
namespace core\handling;

require_once "Middleware.php";

class RouteData
{
    private $middleware = [];
    
    public function addMiddleware(Middleware $middleware)
    {
        array_push($this->middleware, $middleware);
    }
    
    public function getMiddleware(): array 
    {
        return $this->middleware;
    }
}