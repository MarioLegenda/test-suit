<?php

namespace ControlFlowCompiler\MethodTypes;

use ControlFlowCompiler\Arguments\ArgumentInterface;
use ControlFlowCompiler\Arguments\MultipleArguments;
use ControlFlowCompiler\Arguments\NoArgument;
use ControlFlowCompiler\Arguments\SingleArgument;
use StrongType\Exceptions\CriticalTypeException;

class ReturnsFalseMethod extends MethodType
{
    public function checkReturned() {
        if($this->getReturnedValue() !== null) {
            $returnedValue = $this->getReturnedValue();
            if($returnedValue === false) {
                return true;
            }

            return false;
        }

        return false;
    }
} 