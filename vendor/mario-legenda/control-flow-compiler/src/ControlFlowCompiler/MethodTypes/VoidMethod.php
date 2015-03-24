<?php

namespace ControlFlowCompiler\MethodTypes;


use ControlFlowCompiler\Arguments\ArgumentInterface;
use ControlFlowCompiler\Arguments\MultipleArguments;
use ControlFlowCompiler\Arguments\NoArgument;
use ControlFlowCompiler\Arguments\SingleArgument;
use StrongType\Exceptions\CriticalTypeException;

class VoidMethod extends MethodType
{
    public function checkReturned() {
        return true;
    }
} 