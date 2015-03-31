<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 31.3.2015.
 * Time: 11:39
 */

namespace App\ToolsBundle\Helpers\Command\Contracts;


use App\ToolsBundle\Helpers\Command\CommandContext;

interface CommandInterface
{
    function execute(CommandContext $context);
} 