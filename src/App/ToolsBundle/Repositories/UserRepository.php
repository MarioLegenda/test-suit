<?php

namespace App\ToolsBundle\Repositories;


use App\ToolsBundle\Entity\Role;
use App\ToolsBundle\Entity\UserInfo;
use App\ToolsBundle\Entity\User;
use App\ToolsBundle\Repositories\Exceptions\RepositoryException;
use EntityToArray\EntityToArray;

use Doctrine\ORM\Query;

class UserRepository extends Repository
{
    private $user = null;
    private $userInfo = null;
    private $roles = array();

    public function getUserInfoById($id) {
        $qb = $this->em->createQueryBuilder();
        $userInfos = $qb->select(
            'ui.user_id',
            'ui.fields',
            'ui.programming_languages',
            'ui.tools',
            'ui.years_of_experience',
            'ui.future_plans',
            'ui.description')
            ->from('AppToolsBundle:UserInfo', 'ui')
            ->where($qb->expr()->eq('ui.user_id', ':user_id'))
            ->setParameter(':user_id', $id)
            ->getQuery()
            ->getResult(QUERY::HYDRATE_ARRAY);

        if(empty($userInfos)) {
            return null;
        }

        $qb = $this->em->createQueryBuilder();
        $roles = $qb->select('r.role')
            ->from('AppToolsBundle:Role', 'r')
            ->where($qb->expr()->eq('r.user_id', ':user_id'))
            ->setParameter(':user_id', $id)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        if(empty($roles)) {
            return null;
        }

        $ui = $userInfos[0];
        $ui['permitions'] = $roles;

        return $ui;
    }

    public function getUsersById(array $userIds) {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select(array('u'))
            ->from('AppToolsBundle:User', 'u')
            ->where($qb->expr()->in('u.user_id', $userIds))
            ->getQuery()
            ->getResult();

        $eta = new EntityToArray($result, array(
            'getName',
            'getLastname',
            'getUsername',
            'getLogged'
        ));
        $users = $eta
            ->config(array(
                'multidimensional' => true,
                'methodName-keys' => true,
                'only-names' => true
            ))
            ->toArray();

        return $users;
    }

    public function getUserByUsername($username) {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select(array('u'))
            ->from('AppToolsBundle:User', 'u')
            ->where($qb->expr()->eq('u.username', ':username'))
            ->setParameter(':username', $username)
            ->getQuery()
            ->getResult();

        if(empty($result)) {
            return null;
        }

        return $result[0];
    }

    public function getAllUsers() {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select(array('u'))
            ->from('AppToolsBundle:User', 'u')
            ->innerJoin('u.roles', 'r', $qb->expr()->eq('u.user_id', 'r.user_id'))
            ->orderBy('u.logged', 'DESC')
            ->getQuery()
            ->getResult();

        if(empty($result)) {
            return null;
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
    }

    public function getUsernamesById(array $userIds) {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select('u.username')
            ->from('AppToolsBundle:User', 'u')
            ->andWhere('u.user_id IN (:ids)')
            ->setParameter(':ids', $userIds)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        if(empty($result) OR $result === null) {
            return null;
        }

        $numIndexed = array();
        for($i = 0; $i < count($result); $i++) {
            $numIndexed[] = $result[$i]['username'];
        }

        return $numIndexed;
    }

    public function getPaginatedUsers($from, $to) {
        $qb = $this->em->createQueryBuilder();
        $result = $qb->select('u.user_id', 'u.username', 'u.name', 'u.lastname', 'u.logged')
            ->from('AppToolsBundle:User', 'u')
            ->where($qb->expr()->between(
                'u.user_id',
                ':from',
                ':to'
            ))
            ->orderBy('u.logged', 'DESC')
            ->setMaxResults(10)
            ->setParameter(':from', $from)
            ->setParameter(':to', $to)
            ->getQuery()
            ->getResult(Query::HYDRATE_ARRAY);

        if(empty($result) OR $result === null) {
            return array();
        }

        return $result;
    }

    public function createUserFromArray(array $userArray, User $user = null, UserInfo $userInfo = null, array $roles = null) {
        $validValues = array(
            'name' => '',
            'lastname' => '',
            'username' => '',
            'userPassword' => '',
            'userPassRepeat' => '',
            'fields' => '',
            'programming_languages' => '',
            'tools' => '',
            'years_of_experience' => '',
            'future_plans' => '',
            'description' => ''
        );

        $isValid = array_diff_key($validValues, $userArray);

        if( ! empty($isValid)) {
            throw new RepositoryException("RepositoryException: Given values are not equal in UserRepository::createUserFromArray()");
        }

        if($user !== null) {
            $this->user = $user;
        }
        else {
            $user = new User();
            $user->setName($userArray['name']);
            $user->setLastname($userArray['lastname']);
            $user->setUsername($userArray['username']);
            $user->setPassword($userArray['userPassword']);
            $user->setPassRepeat($userArray['userPassRepeat']);

            $this->user = $user;
        }

        if($userInfo !== null) {
            $this->userInfo = $userInfo;
        }
        else {
            $userInfo = new UserInfo();
            $userInfo->setFields($userArray['fields']);
            $userInfo->setProgrammingLanguages($userArray['programming_languages']);
            $userInfo->setTools($userArray['tools']);
            $userInfo->setYearsOfExperience($userArray['years_of_experience']);
            $userInfo->setFuturePlans($userArray['future_plans']);
            $userInfo->setDescription($userArray['description']);

            $this->userInfo = $userInfo;
        }

        if($roles !== null) {
            $this->roles = $roles;
        }
        else {
            $permissions = (array)$userArray['userPermissions'];
            $validPermissions = array('role_test_solver' => '', 'role_test_creator' => '', 'role_user_manager' => '');

            $diff = array_diff_key($permissions, $validPermissions);
            if( ! empty($diff)) {
                throw new RepositoryException('UserRepository: some of the permissions sent from the client are not valid');
            }

            foreach($permissions as $permission => $valid) {
                if($valid === true) {
                    $role = strtoupper($permission);
                    $tempRole = new Role();
                    $tempRole->setRole($role);
                    $tempRole->setUser($this->user);

                    $this->roles[] = $tempRole;
                }
            }
        }

        $encodedPassword = $this->security->encodePassword($this->user, $this->user->getPassword());
        $this->user->setPassword($encodedPassword);
        $this->user->setUserInfo($this->userInfo);
        $this->userInfo->setUser($user);
        foreach($this->roles as $role) {
            $this->user->setRoles($role);
        }

        return $this;
    }

    public function createUser(User $user, array $roles, UserInfo $userInfo = null) {
        $this->user = $user;
        $encodedPassword = $this->security->encodePassword($this->user, $this->user->getPassword());
        $this->user->setPassword($encodedPassword);

        foreach($roles as $role) {
            $temp = new Role();
            $temp->setRole($role);
            $temp->setUser($this->user);

            $this->roles[] = $temp;
        }

        $this->userInfo = ($userInfo !== null) ? $userInfo : new UserInfo();
        $this->userInfo->setUser($this->user);
        $this->user->setUserInfo($this->userInfo);

        foreach($this->roles as $role) {
            $this->user->setRoles($role);
        }

        return $this;
    }

    public function saveUser() {
        try {
            $this->em->persist($this->user);
            $this->em->flush();
        } catch(\Exception $e) {
            echo $e->getMessage();
            die();
        }

        $this->user = null;
        $this->userInfo = null;
        $this->roles = null;
    }

    public function modifyUser($id, array $userArray) {
        $user = $this->em->getRepository('AppToolsBundle:User')->find($id);
        $encodedPassword = $this->security->encodePassword($user, $userArray['userPassword']);

        $user->setName($userArray['name']);
        $user->setLastname($userArray['lastname']);
        $user->setUsername($userArray['username']);
        $user->setPassword($userArray['userPassword']);

        $qb = $this->em->createQueryBuilder();
        $userInfo = $qb->select(array('ui'))
            ->from('AppToolsBundle:UserInfo', 'ui')
            ->where($qb->expr()->eq('ui.user_id', ':user_id'))
            ->setParameter(':user_id', $id)
            ->getQuery()
            ->getResult();

        if(empty($userInfo) OR $userInfo === null) {
            return null;
        }

        $user->setUserInfo($userInfo);
        $userInfo->setUser($user);


    }
} 