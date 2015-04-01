<?php

namespace App\ToolsBundle\Helpers\Command\Filters;


interface FilterInterface
{
    function isValid(array $content);
} 