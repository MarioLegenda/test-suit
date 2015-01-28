<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 26.1.2015.
 * Time: 12:31
 */

namespace App\PublicBundle\Helpers;


use App\PublicBundle\Helpers\Contracts\ClientDependencyInterface;

class DependencyManager
{
    private $dependencies = array();

    public function __construct() {

    }

    public function add($key, ClientDependencyInterface $dependency) {
        if(array_key_exists($key, $this->dependencies)) {
            // do some logging to notify user that dependency has be overwritten
        }

        $this->dependencies[$key] = $dependency;
    }

    public function get($dependency) {
        if( ! array_key_exists($dependency, $this->dependencies)) {
            return null;
        }

        return $this->dependencies[$dependency];
    }
} 