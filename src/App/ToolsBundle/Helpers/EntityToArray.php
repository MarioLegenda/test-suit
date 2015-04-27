<?php

namespace App\ToolsBundle\Helpers;

class EntityToArray
{
    private $entityMethods = array();
    private $entities = array();

    private $config = array(
        'numeric-keys' => false,
        'use-temp' => true,
        'methodName-keys' => false,
        'custom-key' => false
    );

    public function __construct(array $entities, array $entityMethods) {
        $this->entities = $entities;
        $this->entityMethods = $entityMethods;
    }

    public function config(array $config) {
        foreach($config as $cng =>  $value) {
            if( ! array_key_exists($cng, $this->config)) {
                throw new GenericException('EntityToArray: Invalid config given: ' . $cng);
            }

            $this->config[$cng] = $value;
        }

        return $this;
    }

    public function toArray() {
        $entityArr = array();
        $entityIt = new \ArrayIterator($this->entityMethods);
        foreach($this->entities as $entity) {
            $temp = array();
            $entityIt->rewind();
            while($entityIt->valid()) {
                $methodName = $entityIt->current();

                if($this->config['numeric-keys'] === true) {
                    if($this->config['use-temp'] === false) {
                       $entityArr[] = $entity->$methodName();
                    }
                    else {
                        $temp[] = $entity->$methodName();
                    }
                }
                else if($this->config['methodName-keys'] === true) {
                    $temp[$methodName] = $entity->$methodName();
                }
                else if($this->config['custom-key'] !== false) {
                    $cstKey = $this->config['custom-key'];
                    if( ! is_string($cstKey)) {
                        throw new GenericException('EntityToArray: custom-key config has to be an array');
                    }

                    $temp[$cstKey] = $entity->$methodName();
                }

                $entityIt->next();
            }

            if($this->config['use-temp'] === true) {
                $entityArr[] = $temp;
            }
        }

        return $entityArr;
    }
} 