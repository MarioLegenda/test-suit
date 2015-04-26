<?php

namespace App\ToolsBundle\Repositories\Scenario\Conditions;


use App\ToolsBundle\Repositories\Scenario\Contracts\ConditionInterface;
use App\ToolsBundle\Repositories\Scenario\GenericCondition;
use App\ToolsBundle\Repositories\Scenario\Scenarious\CreatePublicTestScenario;
use RCE\Builder\Builder;
use RCE\ContentEval;
use RCE\Filters\BeInteger;
use RCE\Filters\BeString;
use RCE\Filters\Exist;

class PublicTestCondition extends GenericCondition implements ConditionInterface
{
    public function isValidCondition() {
        $builder = new Builder($this->scenarioData);
        $builder->build(
            $builder->expr()->hasTo(new Exist('user_id'), new BeInteger('user_id')),
            $builder->expr()->hasTo(new Exist('test_name'), new BeString('test_name')),
            $builder->expr()->hasTo(new Exist('remarks'), new BeString('remarks'))
        );

        return ContentEval::builder($builder)->isValid();
    }

    public function createScenario() {
        return new CreatePublicTestScenario($this->scenarioData);
    }
} 