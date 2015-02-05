<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 5.2.2015.
 * Time: 0:10
 */

namespace App\ToolsBundle\Repositories\Exceptions;


class RepositoryException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 