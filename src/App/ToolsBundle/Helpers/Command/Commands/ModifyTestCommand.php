<?php

namespace App\ToolsBundle\Helpers\Command\Commands;



use App\ToolsBundle\Helpers\Command\Command;
use App\ToolsBundle\Helpers\Command\CommandContext;
use App\ToolsBundle\Helpers\Command\CommandException;

class ModifyTestCommand extends Command
{
    private $validity = false;

    public function execute(CommandContext $context) {
        if( ! $context->hasParam('modify-test-content')) {
            throw new CommandException('ValidTestCommand expects that the CommandContext has \'modify-test-content\'');
        }

        $testMetadata = $context->getParam('modify-test-content');

        if( ! array_key_exists('test_control_id', $testMetadata)) {
            $this->validity = false;
            return $this;
        }

        if( ! array_key_exists('test_name', $testMetadata)) {
            $this->validity = false;
            return $this;
        }

        if( ! array_key_exists('test_solvers', $testMetadata)) {
            $this->validity = false;
            return $this;
        }

        if( ! is_array($testMetadata['test_solvers'])) {
            $this->validity = false;
            return $this;
        }

        if( ! array_key_exists('remarks', $testMetadata)) {
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