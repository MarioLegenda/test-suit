<?php

namespace ControlFlowCompiler\MethodTypes;

use ControlFlowCompiler\Arguments\ArgumentInterface;
use ControlFlowCompiler\Arguments\MultipleArguments;
use ControlFlowCompiler\Arguments\NoArgument;
use ControlFlowCompiler\Arguments\SingleArgument;
use StrongType\Exceptions\CriticalTypeException;

class ReturnsArrayMethod extends MethodType
{
    public function checkReturned() {
        if($this->getReturnedValue() !== null) {
            $returnedValue = $this->getReturnedValue();

            if( ! is_array($returnedValue)) {
                return false;
            }

            return true;
        }

        return false;
    }
} 