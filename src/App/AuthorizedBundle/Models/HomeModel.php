<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 2.2.2015.
 * Time: 22:03
 */

namespace App\AuthorizedBundle\Models;


use App\ToolsBundle\Models\GenericModel;

class HomeModel extends GenericModel
{

    public function __construct($security) {
        $this->security = $security;
        $this->user = $security->getToken()->getUser();
    }

    public function runModel() {
        $this->modelData['username'] = $this->user->getUsername();
    }
} 