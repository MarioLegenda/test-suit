<?php

namespace App\ToolsBundle\Repositories\Exceptions;

class RepositoryException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 