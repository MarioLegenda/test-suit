<?php

namespace ControlFlowCompiler\MethodTypes\MethodFactory;


use ControlFlowCompiler\DefinitionExaminer;
use ControlFlowCompiler\MethodTypes\ReturnsObjectMethod;
use ControlFlowCompiler\MethodTypes\ReturnsTrueMethod;
use ControlFlowCompiler\MethodTypes\ReturnsFalseMethod;
use ControlFlowCompiler\MethodTypes\VoidMethod;
use ControlFlowCompiler\MethodTypes\ReturnsSelfMethod;
use ControlFlowCompiler\MethodTypes\ReturnsStringMethod;
use ControlFlowCompiler\MethodTypes\ReturnsArrayMethod;
use StrongType\Exceptions\CriticalTypeException;

class MethodFactory extends AbstractMethodFactory
{
    public function createMethod() {
        if($this->examiner->isVoid()) {
            if($this->examiner->hasArguments()) {
                return new VoidMethod(
                    $this->methodDefinition->getMethodName(),
                    $this->methodDefinition->getParameters()
                );
            }

            if( ! $this->examiner->hasArguments()) {
                return new VoidMethod(
                    $this->methodDefinition->getMethodName()
                );
            }

            throw new CriticalTypeException('MethodFactory::createMethod(): Method ' . $this->methodDefinition->getMethodName() . ' was not found by the MethodFactory');
        }

        if($this->examiner->isReturningBool()) {
            if($this->examiner->hasToBeTrue()) {
                if($this->examiner->hasArguments()) {
                    return new ReturnsTrueMethod(
                        $this->methodDefinition->getMethodName(),
                        $this->methodDefinition->getParameters()
                    );
                }

                if( ! $this->examiner->hasArguments()) {
                    return new ReturnsTrueMethod(
                        $this->methodDefinition->getMethodName()
                    );
                }
            }
        }

        if($this->examiner->isReturningBool()) {
            if($this->examiner->hasToBeFalse()) {
                if($this->examiner->hasArguments()) {
                    return new ReturnsFalseMethod(
                        $this->methodDefinition->getMethodName(),
                        $this->methodDefinition->getParameters()
                    );
                }

                if( ! $this->examiner->hasArguments()) {
                    return new ReturnsFalseMethod(
                        $this->methodDefinition->getMethodName()
                    );
                }
            }
        }

        if($this->examiner->isReturningArray()) {
            if ($this->examiner->hasArguments()) {
                return new ReturnsArrayMethod(
                    $this->methodDefinition->getMethodName(),
                    $this->methodDefinition->getParameters()
                );
            }

            if (!$this->examiner->hasArguments()) {
                return new ReturnsArrayMethod(
                    $this->methodDefinition->getMethodName()
                );
            }
        }

        if($this->examiner->doesReturnObject()) {
            if($this->examiner->hasArguments()) {
                return new ReturnsObjectMethod(
                    $this->methodDefinition->getMethodName(),
                    $this->methodDefinition->getParameters()
                );
            }

            if( ! $this->examiner->hasArguments()) {
                return new ReturnsObjectMethod(
                    $this->methodDefinition->getMethodName()
                );
            }
        }

        if($this->examiner->doesReturnString()) {
            if ($this->examiner->hasArguments()) {
                return new ReturnsStringMethod(
                    $this->methodDefinition->getMethodName(),
                    $this->methodDefinition->getParameters()
                );
            }

            if (!$this->examiner->hasArguments()) {
                return new ReturnsStringMethod(
                    $this->methodDefinition->getMethodName()
                );
            }
        }

        if($this->examiner->doesReturnSelf()) {
            if($this->examiner->hasArguments()) {
                return new ReturnsSelfMethod(
                    $this->methodDefinition->getMethodName(),
                    $this->methodDefinition->getParameters()
                );
            }

            if( ! $this->examiner->hasArguments()) {
                return new ReturnsSelfMethod(
                    $this->methodDefinition->getMethodName()
                );
            }
        }
    }
} 