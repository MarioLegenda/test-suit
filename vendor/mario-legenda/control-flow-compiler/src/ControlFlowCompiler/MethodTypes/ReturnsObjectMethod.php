<?php

namespace ControlFlowCompiler\MethodTypes;

use ControlFlowCompiler\Arguments\ArgumentInterface;
use ControlFlowCompiler\Arguments\MultipleArguments;
use ControlFlowCompiler\Arguments\NoArgument;
use ControlFlowCompiler\Arguments\SingleArgument;
use StrongType\Exceptions\CriticalTypeException;

class ReturnsObjectMethod extends MethodType
{
    public function checkReturned() {
        if($this->getReturnedValue() !== null) {
            $returnedValue = $this->getReturnedValue();

            if( ! is_object($returnedValue)) {
                return false;
            }

            return true;
        }

        return false;
    }
}