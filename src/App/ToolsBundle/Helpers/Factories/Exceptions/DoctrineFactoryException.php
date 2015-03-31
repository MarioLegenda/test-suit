<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 31.3.2015.
 * Time: 15:18
 */

namespace App\ToolsBundle\Helpers\Factories\Exceptions;


class DoctrineFactoryException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 