<?php

namespace ControlFlowCompiler\Storage;


use ControlFlowCompiler\Storage\Exceptions\StorageException;

class StorageUnit
{
    private $storage = array();

    public function store($key, $value) {
        if(array_key_exists($key, $this->storage)) {
            throw new StorageException('StorageUnit: StorageUnit::store($key, $value); $key already exists in storage');
        }

        $this->storage[$key] = $value;
    }

    public function retreive($key) {
        if( ! $this->isStored($key)) {
            return null;
        }

        return $this->storage[$key];
    }

    public function isStored($key) {
        return array_key_exists($key, $this->storage);
    }
} 