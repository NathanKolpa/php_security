<?php

require_once("core/handling/RequestController.php");

$controller = new \core\handling\RequestController();

$req = \core\http\HttpRequest::fromThisRequest();
$controller->handleRequest($req);
