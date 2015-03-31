<?php

namespace App\ToolsBundle\Helpers\Command;


class CommandContext
{
    private $context;

    public function addParam($key, $params) {
        $this->context[$key] = $params;
    }

    public function getParam($key) {
        if( ! $this->hasParam($key)) {
            throw new CommandException('CommandContext: No command with key: ' . $key);
        }

        return $this->context[$key];
    }

    public function hasParam($key) {
        if( ! array_key_exists($key, $this->context)) {
            return false;
        }

        return true;
    }
} 