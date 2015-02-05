<?php

namespace App\PublicBundle\Models;

use App\ToolsBundle\Entity\InstallEntity;
use App\ToolsBundle\Entity\UserInfo;
use App\ToolsBundle\Helpers\Contracts\InstallInterface;
use App\ToolsBundle\Helpers\CssClasses;
use App\ToolsBundle\Entity\User;
use App\ToolsBundle\Entity\Role;
use App\ToolsBundle\Helpers\ModelObjectWrapper;
use App\ToolsBundle\Models\GenericModel;

use Doctrine\ORM\EntityManager;

class InstallModel extends GenericModel implements InstallInterface
{
    private $em;
    private $installEntity;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function injectDependencies(InstallEntity $installEntity) {
        $this->installEntity = $installEntity;
    }

    public function runModel() {
        try {
            $user = new User();
            $user->setName($this->installEntity->getName());
            $user->setLastname($this->installEntity->getLastname());
            $user->setUsername($this->installEntity->getUsername());
            $user->setPassword($this->installEntity->getPassword());
        } catch(\Exception $e) {
            echo $e->getMessage();
            die();
        }

        $role_admin = new Role();
        $role_admin->setRole('ROLE_ADMIN');
        $role_admin->setUser($user);

        $role_user = new Role();
        $role_user->setRole('ROLE_USER');
        $role_user->setUser($user);

        $role_super_admin = new Role();
        $role_super_admin->setRole('ROLE_SUPER_ADMIN');
        $role_super_admin->setUser($user);

        $user->setRoles($role_admin);
        $user->setRoles($role_user);
        $user->setRoles($role_super_admin);

        $userInfo = new UserInfo();
        $userInfo->setUser($user);
        $user->setUserInfo($userInfo);

        $wrapper = new ModelObjectWrapper();
        $wrapper->addObject('user', $user);
        $wrapper->addObject('role_admin', $role_admin);
        $wrapper->addObject('role_user', $role_user);
        $wrapper->addObject('role_super_admin', $role_super_admin);
        $wrapper->addObject('user_info', $userInfo);

        return $wrapper;
    }

    public function createViewClasses() {
        $cssClasses = new CssClasses(array(
            'InstallBody' => 'InstallBody',
            'GlobalInstall' => 'GlobalInstall',
            'InstallHeader' => 'InstallHeader'
        ));

        return $cssClasses;
    }
} 