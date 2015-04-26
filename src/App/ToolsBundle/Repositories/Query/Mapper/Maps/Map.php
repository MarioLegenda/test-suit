<?php

namespace App\ToolsBundle\Repositories\Query\Mapper\Maps;

use App\ToolsBundle\Repositories\Query\Mapper\ObservableInterface;

abstract class Map
{
    protected $map = array();
    protected $mapping = array();

    public function update(ObservableInterface $observable) {
        $mapped = array();

        foreach($this->mapping as $key => $ui) {
            if(array_key_exists($key, $this->map)) {
                $filter = $this->map[$key];
                $mapped[$key] = $filter->filter($ui);
                continue;
            }

            $mapped[$key] = $ui;
        }

        return $mapped;
    }
} 