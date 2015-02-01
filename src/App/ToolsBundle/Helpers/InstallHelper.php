<?php

namespace App\ToolsBundle\Helpers;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;

class InstallHelper
{
    private $em;

    public function __construct(EntityManager $manager) {
        $this->em = $manager;
    }

    public function isAppInstalled() {
        $conn = $this->em->getConnection();

        $errors = 0;

        $tables = $conn->fetchAll("SELECT TABLE_CATALOG as admin_catalog FROM INFORMATION_SCHEMA.TABLES
                                   WHERE TABLE_SCHEMA = 'suite' AND TABLE_NAME = 'administrators'
                                   UNION ALL
                                   SELECT TABLE_CATALOG as role_catalog FROM INFORMATION_SCHEMA.TABLES
                                   WHERE TABLE_SCHEMA = 'suite' AND TABLE_NAME = 'roles'");

        return count($tables) === 2;

    }

    public function doesAppHasAdmin() {
        $conn = $this->em->getConnection();

        $admins = $conn->fetchAll("SELECT MAX(admin_id) AS admins FROM administrators");

        return $admins[0]['admins'] !== null;
    }
} 