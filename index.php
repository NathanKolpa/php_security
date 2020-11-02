<?php

use core\handling\Middleware;
use core\handling\Request;
use core\handling\RequestController;
use core\handling\RequestHandler;
use core\handling\ResponseWriter;

require_once("core/handling/RequestController.php");

class Test implements Middleware, RequestHandler, ResponseWriter
{
    function write()
    {
        echo "write!";
    }

    function transform(Request $req, callable $next): ResponseWriter
    {
        return $next($req);
    }

    function handle(Request $req): ResponseWriter
    {
        return $this;
    }
}

// TODO: middleware is not found
// TODO: class voor request values en route data in een

$controller = new RequestController();
$controller->addMiddleware("/", new Test());
$controller->addRoute("GET", "/users/:id", new Test());

$req = \core\http\HttpRequest::fromThisRequest();
$controller->handleRequest($req);
