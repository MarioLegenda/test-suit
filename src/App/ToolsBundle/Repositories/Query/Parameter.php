<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 17.4.2015.
 * Time: 21:29
 */

namespace App\ToolsBundle\Repositories\Query;


class Parameter
{
    private $param;
    private $value;
    private $dataType;

    public function __construct($param, $value, $dataType) {
        $this->param = $param;
        $this->value = $value;
        $this->dataType = $dataType;
    }

    public function param() {
        return $this->param;
    }

    public function value() {
        return $this->value;
    }

    public function dataType() {
        return $this->dataType;
    }
} 