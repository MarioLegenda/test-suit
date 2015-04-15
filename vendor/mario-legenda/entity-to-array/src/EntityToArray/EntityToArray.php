<?php

namespace EntityToArray;


use EntityToArray\Exceptions\EntityToArrayException;

class EntityToArray
{
    private $entityMethods = array();
    private $entities = array();

    private $config = array(
        'numeric-keys' => false,
        'multidimensional' => true,
        'methodName-keys' => false,
        'only-names' => false,
        'custom-key' => false
    );

    public function __construct(array $entities, array $entityMethods) {
        $this->entities = $entities;
        $this->entityMethods = $entityMethods;
    }

    public function config(array $config) {
        foreach($config as $cng =>  $value) {
            if( ! array_key_exists($cng, $this->config)) {
                throw new EntityToArrayException('EntityToArray: Invalid config given: ' . $cng);
            }

            $this->config[$cng] = $value;
        }

        if($this->config['methodName-keys'] === true AND $this->config['multidimensional'] === false) {
            $this->config['multidimensional'] = true;
        }

        if($this->config['multidimensional'] === false AND count($this->entityMethods) > 1) {
            $this->config['multidimensional'] = true;
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
                    if($this->config['multidimensional'] === false) {
                       $entityArr[] = $entity->$methodName();
                    }
                    else {
                        $temp[] = $entity->$methodName();
                    }
                }
                else if($this->config['methodName-keys'] === true) {
                    if($this->config['only-names'] === true) {
                        $nameKey = strtolower(preg_replace('#^(is|get|set)#', '', $methodName));
                        $temp[$nameKey] = $entity->$methodName();
                    }
                    else {
                        $temp[$methodName] = $entity->$methodName();
                    }
                }
                else if($this->config['custom-key'] !== false) {
                    $cstKey = $this->config['custom-key'];
                    if( ! is_string($cstKey)) {
                        throw new EntityToArrayException('EntityToArray: custom-key config has to be an array');
                    }

                    $temp[$cstKey] = $entity->$methodName();
                }

                $entityIt->next();
            }

            if($this->config['multidimensional'] === true) {
                $entityArr[] = $temp;
            }
        }

        return $entityArr;
    }
} 