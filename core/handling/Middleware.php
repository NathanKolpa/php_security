<?php

namespace core\handling;


use core\http\HttpRequest;

require_once "ResponseWriter.php";
require_once "Request.php";

interface Middleware
{
    function transform(Request $req, Callable $next): ResponseWriter;
}