<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 21.4.2015.
 * Time: 13:29
 */

namespace App\ToolsBundle\Repositories\Query\Mapper\Filters;


class DefaultValue implements FilterInterface
{
    private $default;

    public function __construct($default) {
        $this->default = $default;
    }

    public function getDefault() {
        return $this->default;
    }
}
