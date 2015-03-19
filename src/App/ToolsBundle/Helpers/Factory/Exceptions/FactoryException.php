<?php

namespace App\ToolsBundle\Helpers\Factory\Exceptions;


class FactoryException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 