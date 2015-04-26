<?php

namespace App\ToolsBundle\Repositories\Query\Mapper\Maps;


use App\ToolsBundle\Repositories\Query\Mapper\Filters\DefaultValue;
use App\ToolsBundle\Repositories\Query\Mapper\Filters\Filter;
use App\ToolsBundle\Repositories\Query\Mapper\Filters\Integer;
use App\ToolsBundle\Repositories\Query\Mapper\Filters\NonEmptyString;
use App\ToolsBundle\Repositories\Query\Mapper\ObserverInterface;

class UserInfoMap extends Map implements ObserverInterface
{
    public function __construct(array $userInfo) {
        $this->mapping = $userInfo;

        $this->map = array(
            'fields' => new Filter(new NonEmptyString(), new DefaultValue(null)),
            'programming_languages' => new Filter(new NonEmptyString(), new DefaultValue(null)),
            'tools' => new Filter(new NonEmptyString(), new DefaultValue(null)),
            'years_of_experience' => new Filter(new Integer(), new DefaultValue(null)),
            'future_plans' => new Filter(new NonEmptyString(), new DefaultValue(null)),
            'description' => new Filter(new NonEmptyString(), new DefaultValue(null))
        );
    }
} 