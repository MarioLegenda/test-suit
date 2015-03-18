<?php

namespace App\ToolsBundle\Helpers\Factory;


use App\ToolsBundle\Helpers\Factory\AbstractFactory;
use App\ToolsBundle\Helpers\Factory\Exceptions\FactoryException;

class ObjectFactory extends AbstractFactory
{
    private $concreteFactory;

    public function __construct(FactoryDataStorage $concreteFactory) {
        $this->concreteFactory = $concreteFactory;
    }

    public function defineObject($type) {
        $this->concreteFactory->storeType($type);
    }

    public function withObjectDependencies(array $dependencies) {
        if(empty($dependencies)) {
            throw new FactoryException('ObjectFactory::withObjectDependencies(): If dependencies are defined, it has to be an array and cannot be empty');
        }

        $this->concreteFactory->storeObjectDependencies($dependencies);
    }

    public function defineMethod($methodName) {
        $this->concreteFactory->storeMethod($methodName);
    }

    public function withMethodDependencies(array $dependencies) {
        if(empty($dependencies)) {
            throw new FactoryException('ObjectFactory::withMethodDependencies(): If dependencies are defined, it has to be an array and cannot be empty');
        }

        $this->concreteFactory->storeMethodDependencies($dependencies);
    }

    public function willReturnResponse() {

    }

    public function createObject() {

    }

    public function run() {

    }
} 