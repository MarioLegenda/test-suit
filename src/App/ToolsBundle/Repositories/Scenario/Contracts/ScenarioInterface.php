<?php

namespace App\ToolsBundle\Repositories\Scenario\Contracts;


use App\ToolsBundle\Repositories\Query\Connection;

interface ScenarioInterface
{
    function execute(Connection $connection);
} 