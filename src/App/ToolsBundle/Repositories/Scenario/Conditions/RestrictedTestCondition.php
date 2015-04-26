<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 24.4.2015.
 * Time: 21:44
 */

namespace App\ToolsBundle\Repositories\Scenario\Conditions;

use App\ToolsBundle\Repositories\Scenario\Contracts\ConditionInterface;
use App\ToolsBundle\Repositories\Scenario\GenericCondition;
use App\ToolsBundle\Repositories\Scenario\Scenarious\CreateRestrictedTestScenario;
use RCE\Builder\Builder;
use RCE\ContentEval;
use RCE\Filters\BeArray;
use RCE\Filters\BeInteger;
use RCE\Filters\BeString;
use RCE\Filters\Exist;

class RestrictedTestCondition extends GenericCondition implements ConditionInterface
{
    public function isValidCondition() {
        $builder = new Builder($this->scenarioData);
        $builder->build(
            $builder->expr()->hasTo(new Exist('user_id'), new BeInteger('user_id')),
            $builder->expr()->hasTo(new Exist('test_name'), new BeString('test_name')),
            $builder->expr()->hasTo(new Exist('remarks'), new BeString('remarks')),
            $builder->expr()->hasTo(new Exist('test_solvers'), new BeArray('test_solvers'))
        );

        return ContentEval::builder($builder)->isValid();
    }

    public function createScenario() {
        return new CreateRestrictedTestScenario($this->scenarioData);
    }
} 