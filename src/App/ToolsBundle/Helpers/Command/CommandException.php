<?php

namespace App\ToolsBundle\Helpers\Command;


class CommandException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 