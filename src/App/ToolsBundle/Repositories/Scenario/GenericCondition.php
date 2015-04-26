<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 24.4.2015.
 * Time: 17:01
 */

namespace App\ToolsBundle\Repositories\Scenario;


abstract class GenericCondition
{
    protected $scenarioData = array();

    public function addScenarioData(array $scenarioData) {
        $this->scenarioData = $scenarioData;
    }
} 