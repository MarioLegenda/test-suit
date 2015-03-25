<?php

namespace ControlFlowCompiler;

use ControlFlowCompiler\Arguments\ArgumentFactory;
use ControlFlowCompiler\Exceptions\CriticalErrorException;
use ControlFlowCompiler\Exceptions\MethodResolverException;
use ControlFlowCompiler\MethodTypes\Contracts\ReturnsValueInterface;
use ControlFlowCompiler\MethodTypes\Exceptions\CriticalMethodException;
use ControlFlowCompiler\MethodTypes\MethodFactory\MethodFactory;
use ControlFlowCompiler\Storage\StorageUnit;
use StrongType\Exceptions\CriticalTypeException;
use ControlFlowCompiler\Storage\ObjectStorage;

class Compiler
{
    private $workingObject = null;

    private $methods = array();

    private $response = array(
        'failure' => null,
        'success' => null,
    );

    private $resolverFailed = false;
    private $resolverSuccess = false;


    private $objectStorage;
    private $returned;

    public function __construct() {
        $this->objectStorage = new ObjectStorage(new \SplObjectStorage());
    }

    public function runObject($object) {
        if( ! is_object($object)) {
            throw new CriticalErrorException('MethodResolver: MethodResolver::newExecution($object) $object parameter has to be an object');
        }

        $this->methods = array();
        $this->workingObject = $object;

        $this->response['failure'] = null;
        $this->response['success'] = null;

        $this->resolverSuccess = false;
        $this->resolverFailed = false;

        $this->objectStorage->emptyStorage();

        return $this;
    }

    public function method() {
        return new MethodDefinition($this);
    }

    public function save(MethodDefinition $definition) {
        $methodFactory = new MethodFactory($definition);
        $method = $methodFactory->createMethod();
        $this->methods[] = $method;
    }

    public function withMethods($setStack = null) {
        if( ! is_array($setStack)) {
            return $this;
        }

        if(empty($setStack)) {
            return $this;
        }

        foreach($setStack as $methodName => $argument) {
            $definition = new MethodDefinition($this);

            $definition->name($methodName)->withParameters($argument)->void();

            $methodFactory = new MethodFactory($definition);
            $method = $methodFactory->createMethod();
            $this->methods[] = $method;
        }

        return $this;
    }

    public function ifFailsRun(\Closure $closure) {
        $this->response['failure'] = $closure;

        return $this;
    }

    public function ifSuccedesRun(\Closure $closure) {
        $this->response['success'] = $closure;

        return $this;
    }

    public function getReturnedData($methodName) {
        return $this->returned->getReturnedValueFor($methodName);
    }

    public function compile() {
        try {
            foreach($this->methods as $method) {
                $failed = $method->execute($this->workingObject)->checkReturned();

                if($method instanceof ReturnsValueInterface) {
                    $storageUnit = new StorageUnit();
                    $storageUnit->store($method->getMethodName(), $method->getReturned());
                    $this->objectStorage->storeUnit($this->workingObject, $storageUnit);
                }

                if($failed === false) {
                    $this->resolverFailed = true;
                    break;
                }
            }

            $this->resolverSuccess = true;

            return $this;
        }
        catch(CriticalMethodException $e) {
            echo $e->getMessage();
            die();
        }
        catch(CriticalTypeException $e) {
            echo $e->getMessage();
            die();
        }

        return $this;
    }

    public function then() {
        try {
            foreach($this->methods as $method) {
                $failed = $method->execute($this->workingObject)->checkReturned();

                if($method instanceof ReturnsValueInterface) {
                    $returned = new StorageUnit();
                    $returned->store($method->getMethodName(), $returned);
                    $this->objectStorage->storeUnit($this->workingObject, $returned);
                }

                if($failed === false) {
                    $this->resolverFailed = true;
                    break;
                }
            }

            $this->resolverSuccess = true;

            return $this;
        }
        catch(CriticalMethodException $e) {
            echo $e->getMessage();
            die();
        }
        catch(CriticalTypeException $e) {
            echo $e->getMessage();
            die();
        }

        return $this;
    }

    public function useParameterFromMethod($methodName, $object) {

    }

    public function hasFailed() {
        return $this->resolverFailed;
    }

    public function hasSucceded() {
        return $this->resolverSuccess;
    }

    public function getResponse() {
        if($this->resolverFailed === true) {
            if($this->response['failure'] === null) {
                throw new CriticalErrorException('MethodResolver: MethodResolver has failed to resolve method stack but no failure response has been given');
            }

            $context = new Context();
            $context->setObjectStorage($this->objectStorage);
            return $this->response['failure']($context);
        }
        else if($this->resolverSuccess === true) {
            if($this->response['success'] === null) {
                throw new CriticalErrorException('MethodResolver: MethodResolver has failed to resolve method stack but no failure response has been given');
            }

            $context = new Context();
            $context->setObjectStorage($this->objectStorage);
            return $this->response['success']($context);
        }

        throw new CriticalErrorException('MethodResolver::getResponse(): If MethodResolver::getResponse() is called, it has to return a Response');
    }
} 