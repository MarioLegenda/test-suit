<?php

namespace App\ToolsBundle\Helpers\Command\Commands;


use App\ToolsBundle\Helpers\Command\Command;
use App\ToolsBundle\Helpers\Command\CommandContext;
use App\ToolsBundle\Helpers\Command\CommandException;

class UserFilterCommand extends Command
{
    private $validity  = false;

    private $filterType;
    private $key;

    private $content;

    public function execute(CommandContext $context) {
        if( ! $context->hasParam('filtering-content')) {
            throw new CommandException('UserPaginationCommand expects that the CommandContext has \'filtering-content\'');
        }

        $this->content = $context->getParam('filtering-content');

        if( ! array_key_exists('filterType', $this->content)) {
            $this->validity = false;
            return $this;
        }

        $this->filterType = $this->content['filterType'];

        if( ! array_key_exists('key', $this->content)) {
            $this->validity = false;
            return $this;
        }

        $this->key = $this->content['key'];

        if( ! is_array($this->content)) {
            $this->validity = false;
            return $this;
        }

        if(empty($this->content)) {
            $this->validity = false;
            return $this;
        }

        if($this->filterType === 'username-filter') {
            if($this->key !== 'username') {
                $this->validity = false;
                return $this;
            }

            if( ! array_key_exists('username', $this->content)) {
                $this->validity = false;
                return $this;
            }

            $this->validity = true;
            return $this;
        }
        else if($this->filterType === 'personal-filter') {
            if($this->key !== 'personal') {
                $this->validity = false;
                return $this;
            }

            if( ! array_key_exists($this->key, $this->content)) {
                $this->validity = false;
                return $this;
            }

            $content = $this->content[$this->key];

            if( ! array_key_exists('name', $content)) {
                $this->validity = false;
                return $this;
            }

            if( ! array_key_exists('lastname', $content)) {
                $this->validity = false;
                return $this;
            }

            $this->validity = true;
            return $this;
        }

        $this->validity = false;

        return $this;
    }

    public function isValid() {
        return $this->validity;
    }


    public function getType() {
        return $this->filterType;
    }

    public function getPureContent() {
        return $this->content[$this->key];
    }
} 