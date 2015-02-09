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

    public function __construct($security, $user) {
        $this->security = $security;
        $this->user = $user;
    }

    public function runModel() {
        $this->modelData['username'] = $this->user->getUsername();
    }
} 