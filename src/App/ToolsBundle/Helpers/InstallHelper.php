<?php

namespace App\ToolsBundle\Helpers;

use App\ToolsBundle\Repositories\Query\Connection;
use App\ToolsBundle\Repositories\Query\Parameters\Parameters;
use App\ToolsBundle\Repositories\Query\Query;
use App\ToolsBundle\Repositories\Query\QueryHolder;
use App\ToolsBundle\Repositories\Query\Statement\Select;
use StrongType\String;

class InstallHelper
{
    private $conn;

    public function __construct(Connection $conn) {
        $this->conn = $conn;
    }

    public function isAppInstalled() {
        $tables = array(
            'public_tests',
            'restricted_tests',
            'roles',
            'tests',
            'test_control',
            'users',
            'user_info'
        );

        $qh = new QueryHolder($this->conn);

        $tablesSql = new String('
            SELECT
              t.TABLE_NAME AS table_name
              FROM information_schema.TABLES AS t
              WHERE t.TABLE_SCHEMA = \'suit\'
        ');

        $parameters = new Parameters();

        $tablesQuery = new Query($tablesSql, array($parameters), 'fetchAll', \PDO::FETCH_COLUMN);

        $result = $qh->prepare(new Select($tablesQuery))->bind()->execute()->getResult();

        $diff = array_diff($tables, $result[0]);

        return empty($diff);
    }

    public function createTables() {
        $conn = $this->conn->getConnection();
        $handle = fopen(__DIR__ . '/../Resources/xml/tables.txt', 'r');
        while(($line = fgets($handle)) !== false) {
            $conn->exec($line);
        }
    }
} 