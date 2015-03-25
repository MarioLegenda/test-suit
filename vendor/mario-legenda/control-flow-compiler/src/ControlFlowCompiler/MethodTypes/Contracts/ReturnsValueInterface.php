<?php

namespace ControlFlowCompiler\MethodTypes\Contracts;


use ControlFlowCompiler\MethodTypes\ReturnedValue;

interface ReturnsValueInterface
{
    function getReturned();
    function setReturned(ReturnedValue $value);
} 