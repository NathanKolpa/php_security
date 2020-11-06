<?php

namespace core\http;

abstract class HttpMessage
{
    private array $headers = [];
    private string $body;

    public function __construct(array $headers, string $body)
    {
        $this->setBody($body);
        $this->headers = $headers;
    }

    public function setHeader(string $name, string $value)
    {
        $this->headers[$name] = $value;
    }

    public function getHeaders(): array 
    {
        return $this->headers;
    }
    
    public function getHeader(string $name): ?string
    {
        return isset($this->headers[$name]) ? $this->headers[$name] : null;
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