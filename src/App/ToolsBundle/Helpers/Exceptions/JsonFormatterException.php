<?php

namespace App\ToolsBundle\Helpers\Exceptions;


class JsonFormatterException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 