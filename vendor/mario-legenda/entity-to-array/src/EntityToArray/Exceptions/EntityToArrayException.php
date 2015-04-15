<?php

namespace EntityToArray\Exceptions;

class EntityToArrayException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 