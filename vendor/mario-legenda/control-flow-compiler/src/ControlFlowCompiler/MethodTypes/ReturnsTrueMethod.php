<?php

namespace ControlFlowCompiler\MethodTypes;

use ControlFlowCompiler\Arguments\ArgumentInterface;
use ControlFlowCompiler\Arguments\MultipleArguments;
use ControlFlowCompiler\Arguments\NoArgument;
use ControlFlowCompiler\Arguments\SingleArgument;
use ControlFlowCompiler\MethodTypes\Exceptions\CriticalMethodException;
use StrongType\Exceptions\CriticalTypeException;

class ReturnsTrueMethod extends MethodType
{
    public function checkReturned() {
        if($this->getReturnedValue() !== null) {
            $returnedValue = $this->getReturnedValue();
            if($returnedValue === true) {
                return true;
            }

            return false;
        }

        return false;
    }
} 