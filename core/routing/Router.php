<?php

namespace core\routing;

use core\http\HttpRequest;
use function Couchbase\defaultDecoder;

require_once "RoutingResult.php";
require_once "MethodNotAllowedException.php";
require_once "NotFoundException.php";
require_once "core/http/HttpRequest.php";

class Router
{
    private $routerData = ["routes" => []];

    public function setValue(string $method, string $path, $value)
    {
        $routerData = &$this->getOrCreateRouter($path);
        $routerData['values'][strtoupper($method)] = $value;
    }

    public function setHierarchicalValue(string $path, $value)
    {
        $routerData = &$this->getOrCreateRouter($path);
        $routerData['hierarchicalValue'] = $value;
    }
    
    public function getHierarchicalValue(string $path)
    {
        $routerData = &$this->getOrCreateRouter($path);
        return isset($routerData['hierarchicalValue']) ? $routerData['hierarchicalValue'] : null;
    }

    public function route(string $method, string $path): RoutingResult
    {
        $variables = [];
        $hierarchicalValues = [];
        
        $routerData = $this->getRouterFromPath($path, $variables, $hierarchicalValues);

        if (!isset($routerData['values']) || count($routerData['values']) == 0)
            throw new NotFoundException();
        
        if (!isset($routerData['values'][$method]))
            throw new MethodNotAllowedException();

        $value = $routerData['values'][$method];

        return new RoutingResult($variables, $hierarchicalValues, $value);
    }

    private function &getOrCreateRouter(string $path): array
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

        return $routerData;
    }

    private function getRouterFromPath(string $path, array &$variables, array &$hierarchicalValues): array
    {
        $parts = explode('/', $path);

        $routerData = &$this->routerData;
        if(isset($routerData['hierarchicalValue']))
            array_push($hierarchicalValues, $routerData['hierarchicalValue']);
        
        for ($i = 0; $i < count($parts); $i++) {
            $part = $parts[$i];
            if ($part == '')
                continue;

            $hasFound = false;
            
            if(!isset($routerData['routes']))
                throw new NotFoundException();

            foreach ($routerData['routes'] as $routeName => $route) {
                if ($routeName[0] == ':') {
                    $varName = substr($routeName, 1);
                    $variables[$varName] = $part;
                } else if ($routeName != $part)
                    continue;

                $routerData = &$route;
                $hasFound = true;
                
                if(isset($routerData['hierarchicalValue']))
                    array_push($hierarchicalValues, $routerData['hierarchicalValue']);
                
                break;
            }

            if (!$hasFound)
                throw new NotFoundException();
        }

        return $routerData;
    }

}