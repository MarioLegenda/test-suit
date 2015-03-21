<?php

namespace App\ToolsBundle\Helpers\StrongType;


class CriticalTypeException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 