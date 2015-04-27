<?php

namespace App\ToolsBundle\Repositories\Query\Exception;

class QueryException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 