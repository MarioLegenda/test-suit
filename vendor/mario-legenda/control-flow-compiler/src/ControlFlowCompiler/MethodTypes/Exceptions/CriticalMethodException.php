<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 24.3.2015.
 * Time: 17:58
 */

namespace ControlFlowCompiler\MethodTypes\Exceptions;


class CriticalMethodException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 