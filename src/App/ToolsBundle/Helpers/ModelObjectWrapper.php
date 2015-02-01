<?php

namespace App\ToolsBundle\Helpers;


use App\ToolsBundle\Helpers\Contracts\ModelObjectWrapperInterface;
use App\ToolsBundle\Helpers\Exceptions\ModelException;

class ModelObjectWrapper
{
    private $objects = array();

    public function __construct() {

    }

    public function addObject($key, ModelObjectWrapperInterface $object) {
        if(array_key_exists($key, $this->objects)) {
            throw new ModelException('ModelObjectWrapper: Model with key ' . $key . ' is already saved');
        }

        $this->objects[$key] = $object;
    }

    public function getObject($key) {
        if(!array_key_exists($key, $this->objects)) {
            throw new ModelException('ModelObjectWrapper: Model with key ' . $key . ' is not saved');
        }

        return $this->objects[$key];
    }
} 