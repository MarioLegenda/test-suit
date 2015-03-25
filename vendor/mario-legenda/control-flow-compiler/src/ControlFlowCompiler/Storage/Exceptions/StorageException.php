<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 25.3.2015.
 * Time: 2:31
 */

namespace ControlFlowCompiler\Storage\Exceptions;


class StorageException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 