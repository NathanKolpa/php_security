<?php

namespace core\handling;

require_once "core/routing/Router.php";
require_once "core/routing/Router.php";
require_once "Middleware.php";
require_once "RouteData.php";
require_once "RequestHandler.php";
require_once "Request.php";
require_once "writers/error/ErrorWriter.php";
require_once "writers/error/HtmlErrorFormatWriter.php";
require_once "writers/error/JsonErrorFormatWriter.php";
require_once "RouteHandler.php";

use core\handling\writers\ErrorWriter;
use core\handling\writers\HtmlErrorFormatWriter;
use core\handling\writers\JsonErrorFormatWriter;
use core\http\HttpRequest;
use core\routing\MethodNotAllowedException;
use core\routing\NotFoundException;
use core\routing\Router;
use core\routing\RoutingResult;

class RequestController
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function addRoute(string $method, string $path, RequestHandler $handler, Middleware ...$middleware)
    {
        $this->router->setValue($method, $path, new RouteHandler($handler, $middleware));
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
        $this->getWriter($req)->write();
    }

    private function getWriter(HttpRequest $req): ResponseWriter
    {
        try {
            return $this->tryHandleRequest($req);
        } catch (MethodNotAllowedException $e) {
            return new ErrorWriter(405, "Method Not Allowed", "Allowed methods: " . implode(", ", $e->getAllowedMethods()), new JsonErrorFormatWriter());
        } catch (NotFoundException $e) {
            return new ErrorWriter(404, "Not found", "", new JsonErrorFormatWriter());
        }
    }

    private function tryHandleRequest(HttpRequest $req): ResponseWriter
    {
        $routingResult = $this->router->route($req->getMethod(), $req->getPath());


        $middlewareArray = $this->getMiddlewareArrayFromResult($routingResult);

        $lastFunction = function (Request $request) use (&$routingResult) {
            return $routingResult->value->handler->handle($request);
        };

        $i = 0;
        $nextFunction = function (Request $request) use (&$i, &$middlewareArray, &$lastFunction, &$nextFunction, &$routingResult) : ResponseWriter {
            if ($i >= count($middlewareArray))
                return $lastFunction($request);

            $i++;
            return $middlewareArray[$i - 1]->transform($request, $nextFunction);
        };

        return $nextFunction(new Request($routingResult->variables, [], $req->getMethod(), $req->getTarget(), $req->getHeaders(), $req->getBody()));
    }


    private function getMiddlewareArrayFromResult(RoutingResult $routingResult): array
    {
        $middlewareArray = [];

        foreach ($routingResult->hierarchicalValues as $routeData) {
            foreach ($routeData->getMiddleware() as $middleware) {
                array_push($middlewareArray, $middleware);
            }
        }

        foreach ($routingResult->value->middleware as $middleware) {
            array_push($middlewareArray, $middleware);
        }

        return $middlewareArray;
    }
}