<?php

namespace App\ToolsBundle\Repositories\Query\Mapper\Filters;


class Integer implements FilterInterface
{
    private $value;

    public function check($value) {
        if( ! is_numeric($value)) {
            return false;
        }

        $int = (int)$value;
        if( ! is_int($int)) {
            return false;
        }

        return true;
    }

    public function alter($value) {
        $this->value = (int)$value;

        return $this;
    }

    public function getAltered() {
        return $this->value;
    }
} 