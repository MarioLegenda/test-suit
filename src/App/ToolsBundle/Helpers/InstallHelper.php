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
                                   WHERE TABLE_SCHEMA = 'suit' AND TABLE_NAME = 'users'
                                   UNION ALL
                                   SELECT TABLE_CATALOG as role_catalog FROM INFORMATION_SCHEMA.TABLES
                                   WHERE TABLE_SCHEMA = 'suit' AND TABLE_NAME = 'roles'
                                   UNION ALL
                                   SELECT TABLE_CATALOG as role_catalog FROM INFORMATION_SCHEMA.TABLES
                                   WHERE TABLE_SCHEMA = 'suit' AND TABLE_NAME = 'tests'
                                   UNION ALL
                                   SELECT TABLE_CATALOG as role_catalog FROM INFORMATION_SCHEMA.TABLES
                                   WHERE TABLE_SCHEMA = 'suit' AND TABLE_NAME = 'test_control'
                                   UNION ALL
                                   SELECT TABLE_CATALOG as role_catalog FROM INFORMATION_SCHEMA.TABLES
                                   WHERE TABLE_SCHEMA = 'suit' AND TABLE_NAME = 'user_info'");

        return count($tables) === 5;

    }

    public function doesAppHasAdmin() {
        $conn = $this->em->getConnection();

        $admins = $conn->fetchAll("SELECT MAX(user_id) AS admins FROM users");

        return $admins[0]['admins'] !== null;
    }
} 