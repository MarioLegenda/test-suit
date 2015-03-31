<?php

namespace App\ToolsBundle\Helpers\Command\Commands;


use App\ToolsBundle\Helpers\Command\Command;
use App\ToolsBundle\Helpers\Command\CommandContext;
use App\ToolsBundle\Helpers\Command\CommandException;

class ValidUserCommand extends Command
{
    private $validity = false;

    public function execute(CommandContext $context) {
        if( ! $context->hasParam('valid-user-content')) {
            throw new CommandException('ValidUserCommand expects that the CommandContext has \'valid-user-content\'');
        }

        $userData = $context->getParam('valid-user-content');

        $validKeys = array(
            'userPermissions',
            'name',
            'lastname',
            'username',
            'userPassword',
            'userPassRepeat',
            'years_of_experience',
            'fields',
            'programming_languages',
            'tools',
            'future_plans',
            'description',
        );

        foreach($validKeys as $key) {
            if( ! array_key_exists($key, $userData)) {
                $this->validity = false;
                return $this;
            }
        }

        $this->validity = true;
        return $this;
    }

    public function isValid() {
        return $this->validity;
    }
} 