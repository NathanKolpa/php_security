<?php

namespace core\routing;

use core\http\HttpRequest;

require_once "RoutingResult.php";
require_once "MethodNotAllowedException.php";
require_once "NotFoundException.php";
require_once "core/http/HttpRequest.php";

class Router
{
    private $routerData = ["routes" => []];

    public function addRoute(string $method, string $path, $value)
    {
        $parts = explode('/', $path);

        $routerData = &$this->routerData;
        for ($i = 0; $i < count($parts); $i++) {
            $part = $parts[$i];
            if ($part == '')
                continue;

            if (!isset($routerData['routes']))
                $routerData['routes'] = [];
            $routes = &$routerData['routes'];

            if (!isset($routes[$part]))
                $routes[$part] = [];
            $routerData = &$routes[$part];
        }

        $routerData['values'][strtoupper($method)] = $value;
    }

    public function route(string $method, string $path): RoutingResult
    {
        $variables = [];
        $routerData = $this->getRouterFromPath($path, $variables);

        if (!isset($routerData['values'][$method]))
            throw new MethodNotAllowedException();

        $value = $routerData['values'][$method];

        return new RoutingResult($variables, $value);
    }

    private function getRouterFromPath(string $path, array &$variables): array
    {
        $parts = explode('/', $path);

        $routerData = &$this->routerData;
        for ($i = 0; $i < count($parts); $i++) {
            $part = $parts[$i];
            if ($part == '')
                continue;

            $hasFound = false;

            foreach ($routerData['routes'] as $routeName => $route) {
                if ($routeName[0] == ':') {
                    $varName = substr($routeName, 1);
                    $variables[$varName] = $part;
                } else if ($routeName != $part)
                    continue;

                $routerData = &$route;
                $hasFound = true;
                break;
            }

            if (!$hasFound)// not tested
                throw new NotFoundException();
        }

        return $routerData;
    }

}