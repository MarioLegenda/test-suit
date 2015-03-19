<?php

namespace App\ToolsBundle\Helpers\Factory;


class Parameters
{
    private $parameters = array();

    public function __construct(array $parameters) {
        $this->parameters = $parameters;
    }

    public function getParameter($key) {
        if( ! array_key_exists($key, $this->parameters)) {
            return null;
        }

        return $this->parameters[$key];
    }
} 