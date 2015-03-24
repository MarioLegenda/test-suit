<?php
namespace ControlFlowCompiler;


use ControlFlowCompiler\Exceptions\CriticalErrorException;
use ControlFlowCompiler\Exceptions\MethodResolverException;

class Method
{
    private $methodName;
    private $parameter = null;

    private $definition;

    public function __construct(MethodDefinition $definition) {
        $this->definition = $definition;
    }

    public function name($methodName) {
        if( ! is_string($methodName)) {
            throw new CriticalErrorException('Method::name($methodName): $methodName has to be a string');
        }

        $this->methodName = $methodName;

        return $this;
    }

    public function withParameter($parameter) {
        $this->parameter = $parameter;

        $this->definition()->enableArguments();

        return $this;
    }

    public function withParameters() {

    }

    public function getMethodString() {
        return $this->methodName;
    }

    public function definition() {
        return $this->definition;
    }

    public function getParameter() {
        return $this->parameter;
    }


} 