<?php


namespace core\routing;


class RoutingResult
{
    public $variables;
    public $value;
    public $hierarchicalValues;
    
    public function __construct(array $variables, array $hierarchicalValues, $value)
    {
        $this->variables = $variables;
        $this->value = $value;
        $this->hierarchicalValues = $hierarchicalValues;
    }


}