<?php

namespace App\ToolsBundle\Helpers\Command;


use App\ToolsBundle\Helpers\Command\Commands\ModifyTestCommand;
use App\ToolsBundle\Helpers\Command\Commands\UserFilterCommand;
use App\ToolsBundle\Helpers\Command\Commands\UserInfoCommand;
use App\ToolsBundle\Helpers\Command\Commands\UserPaginationCommand;
use App\ToolsBundle\Helpers\Command\Commands\ValidTestCommand;
use App\ToolsBundle\Helpers\Command\Commands\ValidUserCommand;

class CommandFactory
{
    private $closures = array();
    private $objects = array();

    private static $instance;
    private $objectKey;

    private function __construct() {
        $this->closures['user-pagination'] = function() {
            return new UserPaginationCommand();
        };

        $this->closures['user-filter'] = function() {
            return new UserFilterCommand();
        };

        $this->closures['user-info'] = function() {
            return new UserInfoCommand();
        };

        $this->closures['valid-user'] = function() {
            return new ValidUserCommand();
        };

        $this->closures['valid-test'] = function() {
            return new ValidTestCommand();
        };

        $this->closures['valid-modified-test'] = function() {
            return new ModifyTestCommand();
        };
    }

    private static function init() {
        self::$instance = (self::$instance instanceof CommandFactory) ? self::$instance : new CommandFactory();
    }

    public static function construct($objectKey) {
        self::init();

        if( ! array_key_exists($objectKey, self::$instance->closures)) {
            throw new CommandException('CommandFactory: No object with key ' . $objectKey);
        }

        self::$instance->objectKey = $objectKey;

        return self::$instance;
    }

    public function getCommand($fresh = null) {
        if($fresh === true) {
            return $this->closures[$this->objectKey]->__invoke();
        }

        if( ! array_key_exists($this->objectKey, $this->objects)) {
            $this->objects[$this->objectKey] = $this->closures[$this->objectKey]->__invoke();
        }

        $tempObject = $this->objects[$this->objectKey];
        $this->objectKey = null;
        return $tempObject;
    }
} 