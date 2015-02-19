<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 16.2.2015.
 * Time: 17:28
 */

namespace App\AuthorizedBundle\Models;


use App\ToolsBundle\Models\GenericModel;
use App\ToolsBundle\Repositories\TestRepository;

class WorkspaceModel extends GenericModel
{
    private $properties = array();

    public function __construct($security, $user) {
        $this->security = $security;
        $this->user = $user;
    }

    public function runModel() {
        $this->modelData['username'] = $this->user->getUsername();
    }

    public function populateWithClojure(\Closure $populateClojure) {
        $populateClojure($this);
    }

    public function setProperty($key, $prop) {
        $this->properties[$key] = $prop;
    }

    public function getProperty($key) {
        if( ! array_key_exists($key, $this->properties)) {
            return "";
        }

        return $this->properties[$key];
    }
} 