<?php

namespace App\ToolsBundle\Helpers\Factory;

use App\AuthorizedBundle\Models\UserModel;
use App\ToolsBundle\Helpers\Factory\Exceptions\FactoryException;
use App\ToolsBundle\Repositories\UserRepository;
use App\ToolsBundle\Repositories\FilterRepository;

class ObjectFactory
{
    private $types = array();

    private $classType;
    private $objectDependencies = null;

    public function __construct() {
        $this->types['App\\AuthorizedBundle\\Models\\UserModel'] = function() {
            return new UserModel();
        };

        $this->types['App\\ToolsBundle\\Repositories\\UserRepository'] = function(Parameters $params) {
            return new UserRepository($params);
        };

        $this->types['App\\ToolsBundle\\Repositories\\FilterRepository'] = function(Parameters $params) {
            return new FilterRepository($params);
        };
    }

    public function defineType($type) {
        if(array_key_exists($type, $this->types) === false) {
            throw new FactoryException('ObjectFactory: No type ' . $type);
        }

        if( ! class_exists($type)) {
            throw new FactoryException('ObjectFactory: ' . $type . ' does not exist');
        }

        $this->classType = $type;

        return $this;
    }

    public function withObjectDependencies(array $dependencies) {
        if(empty($dependencies)) {
            throw new FactoryException('ObjectFactory: If supplied, dependencies has to be an associative array with keys as variable names');
        }

        $this->objectDependencies = new Parameters($dependencies);

        return $this;
    }

    public function createObject() {
        $callback = $this->types[$this->classType];

        $interfaces = class_implements($this->classType);
        if($interfaces === false) {
            throw new FactoryException('ObjectFactory: Object that is being created with Object factory has to implement ParameterInterface');
        }

        if( ! array_key_exists('App\ToolsBundle\Helpers\Factory\ParameterInterface', $interfaces)) {
            throw new FactoryException('ObjectFactory: Type ' . $this->classType . ' does not implement ParameterInterface');
        }

        if($callback instanceof \Closure) {
            if($this->objectDependencies instanceof Parameters) {
                $object = $callback($this->objectDependencies);
                $this->types[$this->classType] = $object;

                return $object;
            }

            $object = $callback();
            $this->types[$this->classType] = $object;

            return $object;
        }
        else if( ! $callback instanceof \Closure) {
            return $this->types[$this->classType];
        }

        throw new FactoryException('ObjectFactory: Internal error. Could not create object');
    }
} 