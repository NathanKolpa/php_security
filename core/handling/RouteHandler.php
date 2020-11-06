<?php


namespace core\handling;

require_once "RequestHandler.php";

class RouteHandler
{
    public RequestHandler $handler;
    public array $middleware;

    public function __construct(RequestHandler $handler, array $middleware)
    {
        $this->handler = $handler;
        $this->middleware = $middleware;
    }


}