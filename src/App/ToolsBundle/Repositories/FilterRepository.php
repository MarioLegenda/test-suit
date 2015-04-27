<?php

namespace App\ToolsBundle\Repositories;

use App\ToolsBundle\Repositories\Exceptions\RepositoryException;
use App\ToolsBundle\Repositories\Query\Connection;
use App\ToolsBundle\Repositories\Query\Query;
use App\ToolsBundle\Repositories\Query\QueryHolder;
use App\ToolsBundle\Repositories\Query\Parameters\Parameters;
use App\ToolsBundle\Repositories\Query\Statement\Select;
use StrongType\String;

class FilterRepository extends Repository
{
    private $filters = array();
    private $callback = null;
    private $returnData = null;

    public function __construct(Connection $connection) {
        parent::__construct($connection);

        $this->filters['username-filter'] = function($username) {
            $qh = new QueryHolder($this->connection);

            $userSql = new String('
                SELECT
                    u.user_id,
                    u.username,
                    u.name,
                    u.lastname,
                    u.logged
                FROM users AS u
                WHERE u.username LIKE \'%' . $username . '%\'
            ');

            $userQuery = new Query($userSql, array(new Parameters()));

            $result = $qh->prepare(new Select($userQuery))->execute()->getResult();

            return $result[0];
        };

        $this->filters['personal-filter'] = function($personData) {
            $qh = new QueryHolder($this->connection);

            $userSql = new String('
                SELECT
                    u.user_id,
                    u.username,
                    u.name,
                    u.lastname,
                    u.logged
                FROM users AS u
                WHERE u.name LIKE \'%' . $personData['name'] . '%\'
                AND u.lastname LIKE \'%' . $personData['lastname'] . '%\'
                LIMIT 10
            ');

            $userQuery = new Query($userSql, array(new Parameters()));

            $result = $qh->prepare(new Select($userQuery))->execute()->getResult();

            return $result[0];
        };

    }

    public function assignFilter($type) {
        if( ! array_key_exists($type, $this->filters)) {
            throw new RepositoryException('Wrong type ' . $type);
        }

        $this->callback = $this->filters[$type];

        return $this;
    }

    public function runFilter($arguments) {
        $this->returnData = $this->callback->__invoke($arguments);
    }

    public function getRepositoryData() {
        $tempData = $this->returnData;
        $this->returnData = null;
        $this->callback = null;

        return $tempData;
    }
} 