<?php

namespace App\ToolsBundle\Repositories\Scenario\Exceptions;


class ScenarioException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 