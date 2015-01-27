<?php

namespace App\PublicBundle\Models\Exceptions;

class ModelException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 