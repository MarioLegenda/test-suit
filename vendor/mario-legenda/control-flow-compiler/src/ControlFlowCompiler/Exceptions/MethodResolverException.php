<?php

namespace ControlFlowCompiler\Exceptions;


class MethodResolverException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 