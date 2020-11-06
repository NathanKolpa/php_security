<?php


namespace core\handling\writers;
require_once "core/handling/ResponseWriter.php";

use core\handling\ResponseWriter;

class JsonResponseWriter implements ResponseWriter
{
    private array $object;
    
    public function __construct(array $object)
    {
        $this->object = $object;
    }

    function write()
    {
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($this->object);
    }
}