<?php

namespace ControlFlowCompiler;


use ControlFlowCompiler\Exceptions\CriticalErrorException;

class Returned
{
    private $returns = array();

    public function saveReturnedValue($methodName, $value) {
        if( ! array_key_exists($methodName, $this->returns)) {
            $this->returns[$methodName] = $value;

            return $this;
        }

        throw new CriticalErrorException($methodName . ' return value has already been saved. Logical error');
    }

    public function getReturnedValueFor($methodName) {
        if(array_key_exists($methodName, $this->returns)) {
            return $this->returns[$methodName];
        }

        throw new CriticalErrorException('No return value for method ' . $methodName);
    }

    public function clear() {
        $this->returns = array();
    }
} 