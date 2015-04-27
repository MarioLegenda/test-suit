<?php

namespace App\ToolsBundle\Repositories\Query\Mapper\Filters;

class NonEmptyString implements FilterInterface
{
    private $value;

    public function check($value) {
        if( ! is_string($value) OR empty($value)) {
            return false;
        }

        return true;
    }

    public function alter($value) {
        $this->value = $value;

        return $this;
    }

    public function getAltered() {
        return $this->value;
    }
} 