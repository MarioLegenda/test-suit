<?php

namespace App\ToolsBundle\Helpers\Observer\Exceptions;


class ObserverException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 