<?php


namespace core\routing;


class MethodNotAllowedException extends \Exception
{
    private array $allowedMethods;

    public function __construct(array $allowedMethods)
    {
        $this->allowedMethods = $allowedMethods;
    }

    public function getAllowedMethods(): array
    {
        return $this->allowedMethods;
    }
}