<?php

namespace App\ToolsBundle\Repositories\Scenario\Exceptions;


class ScenarioConditionException extends \Exception
{
    public function __construct($message) {
        $this->message = $message;
    }
} 