<?php

namespace App\PublicBundle\Models;

use App\ToolsBundle\Entity\InstallEntity;
use App\PublicBundle\Helpers\Contracts\InstallInterface;
use App\PublicBundle\Helpers\CssClasses;
use App\ToolsBundle\Entity\Administrator;
use App\ToolsBundle\Entity\Role;
use App\PublicBundle\Helpers\ModelObjectWrapper;

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
            $administrator = new Administrator();
            $administrator->setName($this->installEntity->getName());
            $administrator->setLastname($this->installEntity->getLastname());
            $administrator->setUsername($this->installEntity->getUsername());
            $administrator->setPassword($this->installEntity->getPassword());
        } catch(\Exception $e) {
            echo $e->getMessage();
            die();
        }

        $role_admin = new Role();
        $role_admin->setRole('ROLE_ADMIN');
        $role_admin->setAdministrator($administrator);

        $role_user = new Role();
        $role_user->setRole('ROLE_USER');
        $role_user->setAdministrator($administrator);

        $administrator->setRoles($role_admin);
        $administrator->setRoles($role_user);

        $wrapper = new ModelObjectWrapper();
        $wrapper->addObject('administrator', $administrator);
        $wrapper->addObject('role_admin', $role_admin);
        $wrapper->addObject('role_user', $role_user);

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