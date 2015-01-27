<?php

namespace App\PublicBundle\Models;

use App\PublicBundle\Entity\InstallEntity;
use App\PublicBundle\Models\Contracts\DependencyManagerInterface;
use App\PublicBundle\Models\Contracts\InstallInterface;
use App\PublicBundle\Forms\AdministratorType;
use App\PublicBundle\Models\Helpers\CssClasses;

use Doctrine\ORM\EntityManager;

class InstallModel extends GenericModel implements InstallInterface
{
    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function installPartial() {

    }

    public function runModel() {
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