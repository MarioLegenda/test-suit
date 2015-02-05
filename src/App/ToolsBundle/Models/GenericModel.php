<?php

namespace App\ToolsBundle\Models;

abstract class GenericModel
{
    abstract function runModel();

    protected  $modelData = array();
    protected  $user;

    public function setModelData($key, $value) {
        $this->modelData[$key] = $value;
    }

    public function getModelData($key) {
        if(!array_key_exists($key, $this->modelData)) {
            return 'N/A';
        }

        return $this->modelData[$key];
    }

    public function isInRole($roleType) {
        return $this->user->isInRole($roleType);
    }
} 