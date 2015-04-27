<?php

namespace App\ToolsBundle\Repositories\Query\Mapper\Exceptions;

class MapperException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 