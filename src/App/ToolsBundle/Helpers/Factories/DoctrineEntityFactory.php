<?php

namespace App\ToolsBundle\Helpers\Factories;

use App\ToolsBundle\Entity\TestControl;
use App\ToolsBundle\Entity\UserInfo;
use App\ToolsBundle\Helpers\Factories\EntityFactory\AssignedTestsFactory;
use App\ToolsBundle\Helpers\Factories\EntityFactory\TestControlFactory;
use App\ToolsBundle\Helpers\Factories\EntityFactory\UserFactory;
use App\ToolsBundle\Helpers\Factories\EntityFactory\UserInfoFactory;
use App\ToolsBundle\Helpers\Factories\Exceptions\DoctrineFactoryException;

class DoctrineEntityFactory
{
    private static $instance;

    private $closures = array();
    private $entity;
    private $setValues;
    private $factory;

    private function __construct() {
        $this->closures['User'] = function() {
            return new UserFactory();
        };

        $this->closures['UserInfo'] = function() {
            return new UserInfoFactory();
        };

        $this->closures['TestControl'] = function() {
            return new TestControlFactory();
        };

        $this->closures['AssignedTests'] = function() {
            return new AssignedTestsFactory();
        };
    }

    public static function initiate($entity) {
        self::$instance = (self::$instance instanceof self) ? self::$instance : new self();

        if( ! array_key_exists($entity, self::$instance->closures)) {
            throw new DoctrineFactoryException('DoctrineEntityFactory: Entity ' . $entity . ' not found in factory');
        }

        self::$instance->factory = self::$instance->closures[$entity]->__invoke();

        return self::$instance;
    }

    public function with(array $data) {
        $this->setValues = $data;

        $this->factory->addConstructionData($this->setValues);

        return $this;
    }

    public function create() {
        return $this->factory->create();
    }
} 