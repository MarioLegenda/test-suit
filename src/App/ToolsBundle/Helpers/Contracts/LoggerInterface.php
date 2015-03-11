<?php

namespace App\ToolsBundle\Helpers\Contracts;

use App\ToolsBundle\Helpers\AppLogger;

interface LoggerInterface
{
    function setLogger(AppLogger $logger);
} 