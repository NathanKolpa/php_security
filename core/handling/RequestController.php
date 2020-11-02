<?php

namespace core\handling;

require_once "core/routing/Router.php";
require_once "core/http/HttpRequest.php";

use core\http\HttpRequest;
use core\routing\MethodNotAllowedException;
use core\routing\NotFoundException;
use core\routing\Router;

class RequestController
{
    private $router;

    public function __construct()
    {
        $this->router = new Router();
        $this->router->addRoute("get", "/", 1);
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
        $handler = $this->router->route($req->getMethod(), $req->getPath());
    }
}