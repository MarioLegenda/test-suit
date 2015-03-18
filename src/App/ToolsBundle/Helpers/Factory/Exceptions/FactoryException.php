<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 18.3.2015.
 * Time: 16:56
 */

namespace App\ToolsBundle\Helpers\Factory\Exceptions;


class FactoryException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 