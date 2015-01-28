<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 27.1.2015.
 * Time: 23:49
 */

namespace App\PublicBundle\Helpers;


use App\PublicBundle\Helpers\Contracts\ModelObjectWrapperInterface;
use App\PublicBundle\Helpers\Exceptions\ModelException;

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