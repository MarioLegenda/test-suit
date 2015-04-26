<?php

namespace App\ToolsBundle\Repositories\Query\Mapper\Filters;


use App\ToolsBundle\Repositories\Query\Mapper\Exceptions\MapperException;

class Filter
{
    private $filters = array();
    private $default;

    public function __construct() {
        $filters = func_get_args();
        foreach($filters as $filter) {
            if( ! $filter instanceof FilterInterface) {
                throw new MapperException(get_class($this) . ': Filter::__construct() has to accept only App\Toolsbundle\Repositories\Query\Mapper\Filters\FilterInterface types');
            }
        }

        $this->default = $filters[count($filters) - 1];
        unset($filters[count($filters) - 1]);

        $this->filters = $filters;
    }

    public function filter($value) {
        $altered = null;
        foreach($this->filters as $filter) {
            if( ! $filter->check($value)) {
                $altered = $this->default->getDefault();
                break;
            }

            $altered = $filter->alter($value)->getAltered();
        }

        return $altered;
    }
} 