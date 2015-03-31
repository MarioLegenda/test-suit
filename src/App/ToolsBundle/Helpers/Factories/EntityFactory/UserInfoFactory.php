<?php

namespace App\ToolsBundle\Helpers\Factories\EntityFactory;


use App\ToolsBundle\Entity\UserInfo;
use App\ToolsBundle\Helpers\Factories\ConcreteFactoryInterface;

class UserInfoFactory implements ConcreteFactoryInterface
{
    private $data;

    public function addConstructionData(array $data) {
        $this->data = $data;
    }

    public function create() {
        $userInfo = new UserInfo();
        $userInfo->setFields($this->data['fields']);
        $userInfo->setProgrammingLanguages($this->data['programming_languages']);
        $userInfo->setTools($this->data['tools']);
        $userInfo->setYearsOfExperience($this->data['years_of_experience']);
        $userInfo->setFuturePlans($this->data['future_plans']);
        $userInfo->setDescription($this->data['description']);

        return $userInfo;
    }
} 