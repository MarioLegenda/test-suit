<?php

namespace App\ToolsBundle\Repositories\Scenario;


use App\ToolsBundle\Repositories\Scenario\Contracts\ConditionInterface;
use App\ToolsBundle\Repositories\Scenario\Exceptions\ScenarioConditionException;
use StrongType\String;

class Condition
{
    private static $conditions = array();

    private $conditionType;
    private $scenarioData;

    public static function addCondition(String $condition, ConditionInterface $instance) {
        self::$conditions[$condition->toString()] = $instance;
    }

    public function __construct(array $scenarioData) {
        if( ! array_key_exists('condition', $scenarioData)) {
            throw new ScenarioConditionException(get_class($this) . ': Condition::__construct()-> \'condition\' does not exist in scenario data');
        }

        $this->conditionType = $scenarioData['condition'];
        $this->scenarioData = $scenarioData['data'];
    }

    public function conditionExists() {
        return array_key_exists($this->conditionType, self::$conditions);
    }

    public function getConditionType() {
        return $this->conditionType;
    }

    public function getConcreteCondition() {
        if( ! $this->conditionExists()) {
            throw new ScenarioConditionException(get_class($this) . 
                ': Condition::getConcreteCondition()-> Condition ' . $this->conditionType . ' does not exist.
                Call Condition::conditionExists() before Condition::getConditionMethod()');
        }
        
        $condition =  self::$conditions[$this->conditionType];
        $condition->addScenarioData($this->scenarioData);

        return $condition;
    }
} 