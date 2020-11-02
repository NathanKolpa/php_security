<?php

namespace core\http;

abstract class HttpMessage
{
    private $headers = [];
    private $body;

    public function __construct(string $body)
    {
        $this->setBody($body);
    }

    public function setHeader(string $name, string $value)
    {
        $this->headers[$name] = $value;
    }

    public function getHeader(string $name): string
    {
        return $this->headers[$name];
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body)
    {
        $this->body = $body;
    }


}