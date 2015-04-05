<?php

namespace App\ToolsBundle\Helpers\Factories\EntityFactory;

use App\ToolsBundle\Helpers\Factories\ConcreteFactoryInterface;
use App\ToolsBundle\Entity\TestControl;
use RandomLib;

class TestControlFactory implements ConcreteFactoryInterface
{
    private $data = null;

    public function addConstructionData(array $data) {
        $this->data = $data;
    }

    public function create() {
        $factory = new RandomLib\Factory();
        $generator = $factory->getMediumStrengthGenerator();

        $testControl = new TestControl();
        $testControl->setIdentifier($generator->generateString(32));
        $testControl->setTestName($this->data['test_name']);
        $testControl->setVisibility($this->data['test_solvers']);
        $testControl->setRemarks($this->data['remarks']);
        $testControl->setIsFinished(0);

        return $testControl;
    }
} 