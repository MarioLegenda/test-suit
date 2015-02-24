<?php

namespace App\AuthorizedBundle\Models;


use App\ToolsBundle\Models\GenericModel;

class UserInfoModel extends GenericModel
{
    public function __construct($security, $user) {
        $this->security = $security;
        $this->user = $user;
    }

    public function runModel() {
    }

    public function getUser() {
        return $this->user;
    }
} 