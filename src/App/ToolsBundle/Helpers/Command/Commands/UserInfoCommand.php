<?php

namespace App\ToolsBundle\Helpers\Command\Commands;


use App\ToolsBundle\Helpers\Command\Command;
use App\ToolsBundle\Helpers\Command\CommandContext;
use App\ToolsBundle\Helpers\Command\CommandException;

class UserInfoCommand extends Command
{
    private $validity = false;

    public function execute(CommandContext $context) {
        if( ! $context->hasParam('user-info-content')) {
            throw new CommandException('UserInfoCommand expects that the CommandContext has \'user-info-content\'');
        }

        $content = $context->getParam('user-info-content');

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