<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 17.3.2015.
 * Time: 0:01
 */

namespace App\ToolsBundle\Repositories;


use App\ToolsBundle\Helpers\Exceptions\ModelException;
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

        $this->filters['permission-filter'] = function($personData) {
            $qb = $this->em->createQueryBuilder();
            $result = $qb->select(array('u'))
                ->from('AppToolsBundle:User', 'u')
                ->where($qb->expr()->andX(
                    $qb->expr()->like('u.name', ':name'),
                    $qb->expr()->like('u.lastname', ':lastname')
                ))
                ->setParameter(':name', '%' . $personData['name'] . '%')
                ->setParameter(':lastname', '%' . $personData['lastname'] . '%')
                ->getQuery()
                ->getResult();

            if(empty($result)) {
                return array();
            }



            $users = array();
            foreach($result as $user) {
                $temp = array();

                $temp['user_id'] = $user->getUserId();
                $temp['username'] = $user->getUsername();

                $temp['name'] = $user->getName();
                $temp['lastname'] = $user->getLastname();
                $temp['logged'] = $user->getLogged();

                $roles = $user->getRoles();

                foreach($roles as $role) {
                    $temp['role'] = strtolower(substr($role->getRole(), 5));
                }

                $users[] = $temp;
            }

            return $users;
        };

    }

    public function assignFilter($type) {
        if( ! array_key_exists($type, $this->filters)) {
            throw new ModelException('Wrong type ' . $type);
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