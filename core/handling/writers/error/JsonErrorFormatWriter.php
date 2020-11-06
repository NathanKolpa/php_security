<?php


namespace core\handling\writers;

require_once "ErrorFormatWriter.php";
require_once "core/handling/writers/JsonResponseWriter.php";

class JsonErrorFormatWriter implements ErrorFormatWriter
{
    function write(int $statusCode, string $error, string $extraMessage)
    {
        $writer = new JsonResponseWriter([
            'error' => $error,
            'message' => $extraMessage
        ]);
        
        $writer->write();
    }
}