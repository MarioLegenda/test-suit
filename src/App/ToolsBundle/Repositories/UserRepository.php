<?php

namespace App\ToolsBundle\Repositories;


use App\ToolsBundle\Entity\Role;
use App\ToolsBundle\Entity\UserInfo;
use App\ToolsBundle\Entity\User;
use App\ToolsBundle\Repositories\Exceptions\RepositoryException;

class UserRepository
{
    private $em;
    private $security;

    private $user = null;
    private $userInfo = null;
    private $roles = array();

    public function __construct($em, $security) {
        $this->em = $em;
        $this->security = $security;
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

            if($permissions['role_super_admin'] === true) {
                $roleSuperAdmin = new Role('ROLE_SUPER_ADMIN');
                $roleSuperAdmin->setRole('ROLE_SUPER_ADMIN');
                $roleSuperAdmin->setUser($this->user);

                $roleAdmin = new Role();
                $roleAdmin->setRole('ROLE_ADMIN');
                $roleAdmin->setUser($this->user);

                $roleUser = new Role();
                $roleUser->setRole('ROLE_USER');
                $roleUser->setUser($this->user);

                $this->roles[] = $roleSuperAdmin;
                $this->roles[] = $roleAdmin;
                $this->roles[] = $roleUser;
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
} 