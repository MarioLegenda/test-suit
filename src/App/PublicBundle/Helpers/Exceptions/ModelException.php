<?php

namespace App\PublicBundle\Helpers\Exceptions;

class ModelException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 