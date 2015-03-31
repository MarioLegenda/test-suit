<?php

namespace App\ToolsBundle\Helpers\Command;


use App\ToolsBundle\Helpers\Command\Contracts\CommandInterface;

abstract class Command
{
    abstract public function execute(CommandContext $context);
    abstract public function isValid();
} 