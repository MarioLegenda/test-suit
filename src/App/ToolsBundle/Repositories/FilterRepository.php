<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 17.3.2015.
 * Time: 0:01
 */

namespace App\ToolsBundle\Repositories;


use App\ToolsBundle\Helpers\Exceptions\ModelException;
use App\ToolsBundle\Helpers\Factory\Parameters;

class FilterRepository extends Repository
{
    private $filters = array();
    private $callback = null;
    private $returnData = null;

    public function __construct(Parameters $parameters) {
        parent::__construct($parameters);

        $this->filters['username-filter'] = function($username) {
            $qb = $this->em->createQueryBuilder();
            $result = $qb->select(array('u'))
                ->from('AppToolsBundle:User', 'u')
                ->where($qb->expr()->like('u.username', ':username'))
                ->setParameter(':username', '%' . $username . '%')
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

        $this->filters['personal-filter'] = function($personData) {
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