<?php

namespace App\ToolsBundle\Repositories\Scenario;


use App\ToolsBundle\Repositories\Scenario\Conditions\PublicTestCondition;
use App\ToolsBundle\Repositories\Scenario\Conditions\PublicToPublicCondition;
use App\ToolsBundle\Repositories\Scenario\Conditions\PublicToRestrictedCondition;
use App\ToolsBundle\Repositories\Scenario\Conditions\RestrictedTestCondition;
use App\ToolsBundle\Repositories\Scenario\Conditions\RestrictedToPublicCondition;
use App\ToolsBundle\Repositories\Scenario\Conditions\RestrictedToRestrictedCondition;
use App\ToolsBundle\Repositories\Scenario\Exceptions\ScenarioException;
use StrongType\String;

class ScenarioFactory
{
    private static $instance;

    private $condition;

    public static function init() {
        return (self::$instance instanceof self) ? self::$instance : new ScenarioFactory();
    }

    private function __construct() {
        Condition::addCondition(new String('create-public-test'), new PublicTestCondition());
        Condition::addCondition(new String('create-restricted-test'), new RestrictedTestCondition());
        Condition::addCondition(new String('restricted-to-public'), new RestrictedToPublicCondition());
        Condition::addCondition(new String('public-to-restricted'), new PublicToRestrictedCondition());
        Condition::addCondition(new String('public-to-public'), new PublicToPublicCondition());
        Condition::addCondition(new String('restricted-to-restricted'), new RestrictedToRestrictedCondition());
    }

    public function condition(Condition $condition) {
        $this->condition = $condition;

        return $this;
    }

    public function createScenario() {
        if( ! $this->condition->conditionExists()) {
            throw new ScenarioException(get_class($this) . ': ScenarioFactory::create()-> Condition ' . $this->condition->getConditionType() . ' does not exist. Add the condition in ScenarioFactory::__construct()');
        }

        $condition = $this->condition->getConcreteCondition();
        if( ! $condition->isValidCondition()) {
            throw new ScenarioException(get_class($this) . ': ScenarioFactory::create()-> Condition ' . $this->condition->getConditionType() . ' is not a valid condition');
        }

        $concreteFactory = new ConcreteScenarioFactory($condition);
        return $concreteFactory->createScenario();
    }
} 