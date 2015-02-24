<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 23.2.2015.
 * Time: 18:10
 */

namespace App\AuthorizedBundle\Models;


use App\ToolsBundle\Models\GenericModel;

class ManagmentModel extends GenericModel
{

    public function __construct($security) {
        $this->security = $security;
        $this->user = $security->getToken()->getUser();
    }

    public function runModel() {
        $this->modelData['username'] = $this->user->getUsername();
    }
} 