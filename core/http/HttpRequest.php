<?php

namespace core\http;

require_once('HttpMessage.php');

class HttpRequest extends HttpMessage
{
    private $method;

    private $target;
    private $parsedUrl;
    private $parsedParams;

    public function __construct(string $method, string $target, string $body)
    {
        parent::__construct($body);
        $this->setMethod($method);
        $this->setTarget($target);
    }

    public static function fromThisRequest(): HttpMessage
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $target = $_GET['path'];
        $body = file_get_contents('php://input');

        return new HttpRequest($method, $target, $body);
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
    }


    public function getTarget(): string
    {
        return $this->target;
    }

    public function setTarget(string $target)
    {
        $this->parsedUrl = parse_url($target);

        if (isset($this->parsedUrl['query']) && $this->parsedUrl['query'] != '')
            parse_str($this->parsedUrl['query'], $this->parsedParams);

        $this->target = $target;
    }

    public function getPath(): string
    {
        return isset($this->parsedUrl['path']) ? $this->parsedUrl['path'] : '/';
    }

    public function getQuery(): string
    {
        return $this->parsedUrl['query'];
    }

    public function getQueryParam(string $name): string
    {
        return $this->parsedParams[$name];
    }

    public function getFragment(): string
    {
        return $this->parsedUrl['fragment'];
    }
}