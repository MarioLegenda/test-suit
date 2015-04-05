<?php

namespace App\ToolsBundle\Helpers\Factories\EntityFactory;

use App\ToolsBundle\Helpers\Factories\ConcreteFactoryInterface;
use App\ToolsBundle\Entity\AssignedTests;

class AssignedTestsFactory implements ConcreteFactoryInterface
{
    private $data = null;

    public function addConstructionData(array $data) {
        $this->data = $data;
    }

    public function create() {
        if($this->data = null) {
            return new AssignedTests();
        }
    }
} 