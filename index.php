<?php

use app\endpoints\CreateMovieHandler;
use app\endpoints\CreateUserHandler;
use app\endpoints\DeleteMovieRequestHandler;
use app\endpoints\GetAllMoviesRequestHandler;
use app\endpoints\GetSingleMovieRequestHandler;
use app\endpoints\LoginHandler;
use app\endpoints\Test;
use app\middleware\AuthenticationMiddleware;
use app\middleware\AuthorisationMiddleware;
use core\database\Database;
use core\handling\middleware\JsonBodyParser;
use core\handling\RequestController;
require_once("core/handling/RequestController.php");
require_once("core/database/Database.php");
require_once("core/handling/middleware/JsonBodyParser.php");
require_once("app/middleware/AuthenticationMiddleware.php");
require_once("app/middleware/AuthorisationMiddleware.php");
require_once("app/endpoints/UserEndpoints.php");
require_once("app/endpoints/MovieEndpoints.php");


// setup ---------
$database = Database::create("localhost", "php_security", "root", "");
$controller = new RequestController();


$controller->addRoute("POST", "/api/users", new CreateUserHandler($database), new JsonBodyParser(['email', 'password']));
$controller->addRoute("POST", "/api/users/login", new LoginHandler($database), new JsonBodyParser(['email', 'password']));

$controller->addMiddleware("/api/movies", new AuthenticationMiddleware());
$controller->addRoute("POST", "/api/movies", new CreateMovieHandler($database), new JsonBodyParser(['title', 'time', 'genre', 'age_rating']), new AuthorisationMiddleware(1));
$controller->addRoute("GET", "/api/movies", new GetAllMoviesRequestHandler($database));
$controller->addRoute("GET", "/api/movies/:id", new GetSingleMovieRequestHandler($database));
$controller->addRoute("DELETE", "/api/movies/:id", new DeleteMovieRequestHandler($database), new AuthorisationMiddleware(1));

// execute -------
$req = \core\http\HttpRequest::fromThisRequest();
$controller->handleRequest($req);

// teardown ------
$database->close();