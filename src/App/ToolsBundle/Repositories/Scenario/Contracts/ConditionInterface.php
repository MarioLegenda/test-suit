<?php

namespace App\ToolsBundle\Repositories\Scenario\Contracts;


interface ConditionInterface
{
    function isValidCondition();
    function createScenario();
} 