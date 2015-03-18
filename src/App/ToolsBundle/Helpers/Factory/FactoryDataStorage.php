<?php

namespace App\ToolsBundle\Helpers\Factory;


use App\ToolsBundle\Helpers\Factory\Exceptions\FactoryException;
use App\ToolsBundle\Helpers\Factory\Interfaces\DefinedTypesInterface;

class FactoryDataStorage
{
    private $definedTypes;

    private $class;
    private $method;
    private $objectDependencies = array();
    private $methodDependencies = array();

    public function __construct(DefinedTypesInterface $types) {
        if( ! $types->hasAnyType()) {
            throw new FactoryException('DefinedTypes: No defined types exist');
        }

        $this->definedTypes = $types;
    }

    public function storeType($type) {
        if( ! $this->definedTypes->hasType($type)) {
            throw new FactoryException('ConcreteFactory: No defined type ' . $type);
        }

        $this->class = $type;
    }

    public function storeMethod($methodName) {
        $this->method = $methodName;
    }

    public function storeObjectDependencies(array $dependencies) {
        $this->objectDependencies = $dependencies;
    }

    public function storeMethodDependencies(array $dependencies) {
        $this->methodDependencies = $dependencies;
    }

    public function getStoredData() {
        return array(
            'class' => $this->class,
            'method' => $this->method,
            'object-dependencies' => $this->objectDependencies,
            'method-dependencies' => $this->methodDependencies
        );
    }
} 