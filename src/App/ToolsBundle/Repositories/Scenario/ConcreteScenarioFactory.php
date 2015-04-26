<?php

namespace App\ToolsBundle\Repositories\Scenario;


use App\ToolsBundle\Repositories\Scenario\Contracts\ConcreteScenarioFactoryInterface;
use App\ToolsBundle\Repositories\Scenario\Contracts\ConditionInterface;
use App\ToolsBundle\Repositories\Scenario\Exceptions\ScenarioConditionException;

class ConcreteScenarioFactory implements ConcreteScenarioFactoryInterface
{
    private $condition;

    public function __construct(ConditionInterface $condition) {
        $this->condition = $condition;
    }

    public function createScenario() {
        if( ! $this->condition->isValidCondition()) {
            throw new ScenarioConditionException(get_class($this->condition) . ' is not a valid condition');
        }

        return $this->condition->createScenario();
    }
} 