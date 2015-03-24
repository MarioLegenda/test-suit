<?php

namespace ControlFlowCompiler\Arguments;


class NoArgument implements ArgumentInterface
{
    public function getArguments() {
        return false;
    }
} 