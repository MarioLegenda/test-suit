<?php

namespace ControlFlowCompiler\MethodFilter;


class MethodFilter
{
    private $methods;

    public function __construct(array $methods) {
        $this->methods = $methods;
    }
} 