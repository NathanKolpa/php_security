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

class AuthorisationMiddleware implements Middleware
{
    private int $minLevel;

    public function __construct(int $minLevel)
    {
        $this->minLevel = $minLevel;
    }

    function transform(Request $req, callable $next): ResponseWriter
    {
        $level = $req->getMiddlewareData('user')['auth_level'];
        if($this->minLevel > $level)
            return new ErrorWriter(403, "Access denied", '', new JsonErrorFormatWriter());
        
        return $next($req);
    }
}