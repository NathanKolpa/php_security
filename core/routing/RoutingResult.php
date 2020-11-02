<?php


namespace core\routing;


class RoutingResult
{
    public $variables;
    public $value;
    
    public function __construct(array $variables, $value)
    {
        $this->variables = $variables;
        $this->value = $value;
    }


}