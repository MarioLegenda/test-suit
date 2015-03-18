<?php

namespace App\ToolsBundle\Helpers\Factory;


use App\ToolsBundle\Helpers\Factory\Exceptions\FactoryException;
use App\ToolsBundle\Helpers\Factory\Interfaces\DefinedTypesInterface;

class DefinedTypes implements DefinedTypesInterface
{
    private $types = array();

    public function __construct(array $types) {
        $this->types = $types;
    }

    public function hasAnyType() {
        return !empty($this->types);
    }

    public function hasType($type) {
        if(in_array($type, $this->types) !== false) {
            return true;
        }

        return false;
    }

    public function isValidType($type) {
        return class_exists($type);
    }

    public function getType($type) {
        $index = array_search($type, $this->types);

        if($index === false) {
            throw new FactoryException('DefinedTypes: No defined type ' . $type);
        }

        return $this->types[$index];
    }
} 