<?php

namespace App\ToolsBundle\Helpers\Factories\EntityFactory;

use App\ToolsBundle\Helpers\Factories\ConcreteFactoryInterface;
use App\ToolsBundle\Entity\User;

class UserFactory implements ConcreteFactoryInterface
{
    private $data;

    public function addConstructionData(array $data) {
        $this->data = $data;
    }

    public function create() {
        $user = new User();
        $user->setName($this->data['name']);
        $user->setLastname($this->data['lastname']);
        $user->setUsername($this->data['username']);
        $user->setPassword($this->data['userPassword']);
        $user->setPassRepeat($this->data['userPassRepeat']);

        return $user;
    }
} 