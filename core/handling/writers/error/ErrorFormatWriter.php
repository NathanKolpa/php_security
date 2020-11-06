<?php


namespace core\handling\writers;


interface ErrorFormatWriter
{
    function write(int $statusCode, string $error, string $extraMessage);
}