<?php

namespace App\ToolsBundle\Helpers\Exceptions;

class GenericException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 