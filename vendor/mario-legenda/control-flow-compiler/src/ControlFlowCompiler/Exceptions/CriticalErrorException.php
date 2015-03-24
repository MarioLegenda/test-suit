<?php

namespace ControlFlowCompiler\Exceptions;


class CriticalErrorException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 