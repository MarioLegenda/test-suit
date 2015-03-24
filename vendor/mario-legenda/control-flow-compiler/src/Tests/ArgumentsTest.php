<?php

require __DIR__ . '/../../vendor/autoload.php';

use ControlFlowCompiler\Arguments\ArgumentFactory;
use ControlFlowCompiler\Arguments\NoArgument;
use ControlFlowCompiler\Arguments\SingleArgument;
use ControlFlowCompiler\Arguments\MultipleArguments;

class ArgumentsTest extends \PHPUnit_Framework_TestCase
{
    public function testNoArgument() {
        $arg = new NoArgument();

        $this->assertFalse($arg->getArguments(), 'testNoArgument(): NoArgument::getArguments() has not returned false but it had to');
    }

    public function testSingleArgument() {
        $arg1 = new SingleArgument(array());
        $args = $arg1->getArguments();

        $this->assertInternalType('array', $args, 'testSingleArgument(): SingleArgument::getArguments() does not return array with
        constructor new SingleArgument(array())');

        $arg2 = new SingleArgument('something', 'nothing');

        $this->assertEquals('something', $arg2->getArguments(), 'testSingleArgument(): SingleArguments() has to return string \'something\'');
    }

    public function testMultipleArguments() {
        $arg1 = new MultipleArguments(array('arg1', 'arg2'));
        $args = $arg1->getArguments();

        $this->assertInternalType('array', $args, 'testMultipleArguments(): MultipleArguments::getArguments() has to return an array. Some other type returned');
    }

    public function testArgumentFactory() {
        $arg = ArgumentFactory::createArgument(array());

        $this->assertInstanceOf('ControlFlowCompiler\\Arguments\\NoArgument', $arg, 'testArgumentFactory(): Return value has to be NoArguments class');

        $arg = ArgumentFactory::createArgument(array('mario'));
        $this->assertInstanceOf('ControlFlowCompiler\\Arguments\\SingleArgument', $arg, 'testArgumentFactory(): Return value has to be SingleArgument class');

        $arg = ArgumentFactory::createArgument(array('mario', 'legenda'));
        $this->assertInstanceOf('ControlFlowCompiler\\Arguments\\MultipleArguments', $arg, 'testArgumentFactory(): Return value has to be MultipleArguments');
    }
} 