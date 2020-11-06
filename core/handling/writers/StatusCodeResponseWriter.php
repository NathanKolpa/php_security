<?php

namespace core\handling\writers;
require_once "core/handling/ResponseWriter.php";

use core\handling\ResponseWriter;

class StatusCodeResponseWriter implements ResponseWriter
{
    private int $status;

    public function __construct(int $status)
    {
        $this->status = $status;
    }

    function write()
    {
        http_response_code($this->status);
    }
}