<?php

namespace ControlFlowCompiler\MethodTypes\MethodFactory;


use ControlFlowCompiler\DefinitionExaminer;
use ControlFlowCompiler\MethodDefinition;

abstract class AbstractMethodFactory
{
    protected $methodDefinition;
    protected $examiner;

    public function __construct(MethodDefinition $definition) {
        $this->methodDefinition = $definition;
        $this->examiner = new DefinitionExaminer($definition);
    }

    abstract function createMethod();
} 