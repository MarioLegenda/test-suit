<?php

namespace ControlFlowCompiler\MethodTypes;

use ControlFlowCompiler\Arguments\ArgumentInterface;
use ControlFlowCompiler\Arguments\MultipleArguments;
use ControlFlowCompiler\Arguments\NoArgument;
use ControlFlowCompiler\Arguments\SingleArgument;
use ControlFlowCompiler\MethodTypes\Contracts\ReturnsValueInterface;
use StrongType\Exceptions\CriticalTypeException;

class ReturnsArrayMethod extends MethodType implements ReturnsValueInterface
{
    private $returned;

    public function checkReturned() {
        if($this->getReturnedValue() !== null) {
            $returnedValue = $this->getReturnedValue();

            if( ! is_array($returnedValue)) {
                return false;
            }

            $this->returned->setValue($returnedValue);

            return true;
        }

        return false;
    }

    public function setReturned(ReturnedValue $value) {
        $this->returned = $value;
    }

    public function getReturned() {
        return $this->returned;
    }
} 