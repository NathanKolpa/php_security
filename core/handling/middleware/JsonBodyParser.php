<?php


namespace core\handling\middleware;

require_once "core/handling/Middleware.php";
require_once "core/handling/writers/error/ErrorWriter.php";
require_once "core/handling/writers/error/JsonErrorFormatWriter.php";

use core\handling\Middleware;
use core\handling\Request;
use core\handling\ResponseWriter;
use core\handling\writers\ErrorWriter;
use core\handling\writers\JsonErrorFormatWriter;

class JsonBodyParser implements Middleware
{
    private array $requiredFields;

    public function __construct(array $requiredFields)
    {
        $this->requiredFields = $requiredFields;
    }


    function transform(Request $req, callable $next): ResponseWriter
    {
        if(!($body = json_decode($req->getBody(), true)))
        {
            return new ErrorWriter(419, "Invalid body", "", new JsonErrorFormatWriter());
        }
        
        $missingFields = [];
        $fields = [];
        
        foreach ($this->requiredFields as $requiredField)
        {
            if(!isset($body[$requiredField]))
            {
                array_push($missingFields, $requiredField);
                continue;
            }

            $fields[$requiredField] = $body[$requiredField];
        }
        
        if(count($missingFields) > 0)
        {
            return new ErrorWriter(419, "Missing fields", "fields missing: " . implode(", ", $missingFields), new JsonErrorFormatWriter());
        }
        
        $req->addMiddlewareData("body", $fields);
        return $next($req);
    }
}