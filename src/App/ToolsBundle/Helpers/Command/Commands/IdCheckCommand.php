<?php

namespace App\ToolsBundle\Helpers\Command\Commands;

use App\ToolsBundle\Helpers\Command\Command;
use App\ToolsBundle\Helpers\Command\CommandContext;
use App\ToolsBundle\Helpers\Command\CommandException;

class IdCheckCommand extends Command
{
    private $validity = false;

    public function execute(CommandContext $context) {
        if( ! $context->hasParam('id-content')) {
            throw new CommandException('IdCheckCommand expects that the CommandContext has \'id-content\'');
        }

        $content = $context->getParam('id-content');

        if( ! array_key_exists('id', $content) OR empty($content['id'])) {
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