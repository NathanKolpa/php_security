<?php


namespace core\handling\writers;

require_once "core/handling/ResponseWriter.php";
require_once "ErrorFormatWriter.php";

use core\handling\ResponseWriter;

class ErrorWriter implements ResponseWriter
{
    private string $error;
    private int $statusCode;
    private string $extraMessage;
    private ErrorFormatWriter $format;

    public function __construct(int $statusCode, string $error, string $extraMessage, ErrorFormatWriter $format)
    {
        $this->error = $error;
        $this->statusCode = $statusCode;
        $this->extraMessage = $extraMessage;
        $this->format = $format;
    }

    function write()
    {
        http_response_code($this->statusCode);
        $this->format->write($this->statusCode, $this->error, $this->extraMessage);
    }
}