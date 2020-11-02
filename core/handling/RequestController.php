<?php

namespace core\handling;

require_once "core/routing/Router.php";
require_once "core/routing/Router.php";
require_once "Middleware.php";
require_once "RouteData.php";
require_once "RequestHandler.php";
require_once "Request.php";

use core\http\HttpRequest;
use core\routing\MethodNotAllowedException;
use core\routing\NotFoundException;
use core\routing\Router;
use core\routing\RoutingResult;

class RequestController
{
    private $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function addRoute(string $method, string $path, RequestHandler $handler)
    {
        $this->router->setValue($method, $path, $handler);
    }

    public function addMiddleware(string $path, Middleware $middleware)
    {
        $routeData = $this->router->getHierarchicalValue($path);
        if (!$routeData) {
            $routeData = new RouteData();
            $this->router->setHierarchicalValue($path, $routeData);
        }

        $routeData->addMiddleware($middleware);
    }

    public function handleRequest(HttpRequest $req)
    {
        try {
            $this->tryHandleRequest($req);
        } catch (MethodNotAllowedException $e) {
            die("method not allowed");
        } catch (NotFoundException $e) {
            die("not found");
        }
    }

    private function tryHandleRequest(HttpRequest $req)
    {
        $routingResult = $this->router->route($req->getMethod(), $req->getPath());
        
        
        $middlewareArray = $this->getMiddlewareArrayFromResult($routingResult);

        $lastFunction = function (Request $request) use (&$routingResult) {
            return $routingResult->value->handle($request);
        };
        
        $i = 0;
        $nextFunction = function (Request $request) use (&$i, &$middlewareArray, &$lastFunction, &$nextFunction, &$routingResult) : ResponseWriter {
            if ($i >= count($middlewareArray))
                return $lastFunction($request);

            $i++;
            return $middlewareArray[$i - 1]->transform($request, $nextFunction);
        };
        
        $writer = $nextFunction(new Request($routingResult->variables, [], $req->getMethod(), $req->getTarget(), $req->getBody()));
        $writer->write();
    }
    
    
    private function getMiddlewareArrayFromResult(RoutingResult $routingResult): array {
        $middlewareArray = [];

        foreach ($routingResult->hierarchicalValues as $routeData) {
            foreach ($routeData->getMiddleware() as $middleware) {
                array_push($middlewareArray, $middleware);
            }
        }
        
        return $middlewareArray;
    }
}