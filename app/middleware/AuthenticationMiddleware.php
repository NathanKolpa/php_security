<?php


namespace app\middleware;

require_once "core/handling/Middleware.php";
require_once "core/crypto/JwtToken.php";
require_once "core/crypto/InvalidTokenException.php";

use core\crypto\InvalidTokenException;
use core\crypto\JwtToken;
use core\handling\Middleware;
use core\handling\Request;
use core\handling\ResponseWriter;
use core\handling\writers\ErrorWriter;
use core\handling\writers\JsonErrorFormatWriter;

class AuthenticationMiddleware implements Middleware
{
    function transform(Request $req, callable $next): ResponseWriter
    {
        if (!($authHeader = $req->getHeader("Authorization")))
            return new ErrorWriter(401, "Authorization header required", 'Include the Authorization in your request', new JsonErrorFormatWriter());

        if (substr($authHeader, 0, strlen('Bearer ')) == 'Bearer ') {
            $authHeader = substr($authHeader, strlen('Bearer '));
        }

        try {
            $jwtToken = JwtToken::fromString($authHeader, new \DateTime());
        } catch (InvalidTokenException $e) {
            return new ErrorWriter(401, "Invalid token", 'The token is either expired, invalidly formatted or tampered with', new JsonErrorFormatWriter());
        }

        $req->addMiddlewareData('user', $jwtToken->getPayload());

        return $next($req);
    }
}