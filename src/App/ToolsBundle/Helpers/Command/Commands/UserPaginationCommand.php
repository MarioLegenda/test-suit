<?php

namespace App\ToolsBundle\Helpers\Command\Commands;


use App\ToolsBundle\Helpers\Command\Command;
use App\ToolsBundle\Helpers\Command\CommandContext;
use App\ToolsBundle\Helpers\Command\CommandException;

class UserPaginationCommand extends Command
{
    private $validity = false;

    public function execute(CommandContext $context) {
        if( ! $context->hasParam('pagination-content')) {
            throw new CommandException('UserPaginationCommand expects that the CommandContext has \'pagination-content\'');
        }

        $content = $context->getParam('pagination-content');
        if( ! array_key_exists('start', $content) OR ! array_key_exists('end', $content)) {
            $this->validity = false;
            return $this;
        }

        $this->validity = true;

        return $this;
    }

    public function isValid() {
        return $this->validity;
    }
} 