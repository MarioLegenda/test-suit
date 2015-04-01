<?php

namespace App\ToolsBundle\Helpers\Command\Commands;


use App\ToolsBundle\Helpers\Command\Command;
use App\ToolsBundle\Helpers\Command\CommandContext;
use App\ToolsBundle\Helpers\Command\CommandException;
use App\ToolsBundle\Helpers\Command\Filters\FilterInterface;

class ConfigurableCommand extends Command
{
    private $validity = false;

    public function execute(CommandContext $context) {
        $this->examineValidity($context);

        $filters = $context->getParam('filters');
        $content = $context->getParam('evaluate-data');

        foreach($filters as $filter) {
            if( ! $filter->isValid($content)) {
                $this->validity = false;
                return $this;
            }
        }

        $this->validity = true;
        return $this;
    }

    private function examineValidity($context) {
        if( ! $context->hasParam('filters')) {
            throw new CommandException('ConfigurableCommand expects that the CommandContext has \'filters\'');
        }

        if( ! $context->hasParam('evaluate-data')) {
            throw new CommandException('ConfigurableCommand expects that the CommandContext has \'evaluate-data\'');
        }

        $filters = $context->getParam('filters');
        if( ! is_array($filters)) {
            throw new CommandException('ConfigurableCommand expects for \'filters\' to be an array');
        }

        if(empty($filters)) {
            throw new CommandException('ConfigurableCommand expects for \'filters\' not to be an empty array');
        }

        foreach($filters as $filter) {
            if( ! $filter instanceof FilterInterface) {
                throw new CommandException('ConfigurableCommand expects that \'filters\' are FilterInterface types');
            }
        }
    }

    public function isValid() {
        return $this->validity;
    }
} 