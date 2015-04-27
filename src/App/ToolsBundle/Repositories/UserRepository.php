<?php

namespace App\ToolsBundle\Repositories;


use App\ToolsBundle\Repositories\Query\Exception\QueryException;
use App\ToolsBundle\Repositories\Query\Mapper\Mapper;
use App\ToolsBundle\Repositories\Query\Mapper\Maps\UserInfoMap;
use App\ToolsBundle\Repositories\Query\Statement\Delete;
use App\ToolsBundle\Repositories\Query\Statement\Insert;
use App\ToolsBundle\Repositories\Query\Statement\Select;

use App\ToolsBundle\Repositories\Query\Parameters\Parameters;
use App\ToolsBundle\Repositories\Query\Query;

use App\ToolsBundle\Repositories\Query\QueryHolder;
use StrongType\String;

class UserRepository extends Repository
{
    /**
     * Controller: UserController::createUserAction()
     */
    public function createUser(array $userArray) {
        $qh = new QueryHolder($this->connection);

        $createUserSql = new String('
            INSERT INTO users (username, password, name, lastname, logged) VALUES(:username, :password, :name, :lastname, NOW())
        ');

        $parameters = new Parameters();
        $parameters->attach(':username', $userArray['username'], \PDO::PARAM_STR);
        $parameters->attach(':password', $userArray['userPassword'], \PDO::PARAM_STR);
        $parameters->attach(':name', $userArray['name'], \PDO::PARAM_STR);
        $parameters->attach(':lastname', $userArray['lastname'], \PDO::PARAM_STR);

        $userQuery = new Query($createUserSql, array($parameters));

        $qh->prepare(new Insert($userQuery))->bind()->execute();

        $lastInsertedId = $qh->getStatement()->getLastInsertedId();

        try {
            $this->createRoles($userArray['userPermissions'], $lastInsertedId);
        }
        catch(QueryException $e) {
            $this->deleteUserOnAtomicFail($lastInsertedId);
            throw new QueryException($e->getMessage());
        }

        $mapper = new Mapper();
        $mapper->attach(new UserInfoMap($userArray));
        $mapper->notify();

        $mapped = $mapper->offsetGetMapped(0);


        try {
            $this->createUserInfo($mapped, $lastInsertedId);
        }
        catch(QueryException $e) {
            $this->deleteUserOnAtomicFail($lastInsertedId);
            throw new QueryException($e->getMessage());
        }
    }


    /**
     * No controller: called within UserRepository::createUser()
     */
    private function createRoles(array $roles, $userId) {
        $rolesToInsert = array();
        foreach($roles as $role => $valid) {
            if($valid === true) {
                $rolesToInsert[] = strtoupper($role);
            }
        }

        $qh = new QueryHolder($this->connection);
        $rolesSql = new String('INSERT INTO roles (user_id, role) VALUES (:user_id, :role)');

        $params = array();
        foreach($rolesToInsert as $role) {
            $parameters = new Parameters();
            $parameters->attach(':user_id', $userId, \PDO::PARAM_INT);
            $parameters->attach(':role', $role, \PDO::PARAM_STR);

            $params[] = $parameters;
        }

        $roleQuery = new Query($rolesSql, $params);

        $qh->prepare(new Insert($roleQuery))->bind()->execute();
    }

    /**
     * No controller: called within UserRepository::createUser()
     */
    private function createUserInfo(array $userInfo, $userId) {
        $qh = new QueryHolder($this->connection);

        $userInfoSql = new String('
            INSERT INTO user_info (user_id, fields, programming_languages, tools, years_of_experience, future_plans, description)
            VALUES (:user_id, :fields, :programming_languages, :tools, :years_of_experience, :future_plans, :description)
        ');

        $parameters = new Parameters();
        $parameters->attach(':user_id', $userId, \PDO::PARAM_INT);
        $parameters->attach(':fields', $userInfo['fields'], \PDO::PARAM_STR);
        $parameters->attach(':tools', $userInfo['tools'], \PDO::PARAM_STR);
        $parameters->attach(':programming_languages', $userInfo['programming_languages'], \PDO::PARAM_STR);
        $parameters->attach(':years_of_experience', $userInfo['years_of_experience'], \PDO::PARAM_INT);
        $parameters->attach(':future_plans', $userInfo['future_plans'], \PDO::PARAM_STR);
        $parameters->attach(':description', $userInfo['description'], \PDO::PARAM_STR);

        $userInfoQuery = new Query($userInfoSql, array($parameters));

        $qh->prepare(new Insert($userInfoQuery))->bind()->execute();

    }

    /**
     * No controller: called within UserRepository::createUser() when atomic insert into 'users' table fails
     */
    private function deleteUserOnAtomicFail($userId) {
        $qh = new QueryHolder($this->connection);

        $deleteRolesSql = new String('DELETE FROM roles WHERE user_id = :user_id');
        $deleteUserInfoSql = new String('DELETE FROM user_info WHERE user_id = :user_id');
        $deleteUserSql = new String('DELETE FROM users WHERE user_id = :user_id');

        $parameters = new Parameters();
        $parameters->attach(':user_id', $userId, \PDO::PARAM_INT);

        $roleQuery = new Query($deleteRolesSql, array($parameters));
        $userInfoQuery = new Query($deleteUserInfoSql, array($parameters));
        $deleteUserQuery = new Query($deleteUserSql, array($parameters));

        $qh->prepare(new Delete($roleQuery, $userInfoQuery, $deleteUserQuery))->bind()->execute();
    }

    public function getUserInfoById($id) {
        $qh = new QueryHolder($this->connection);

        $userInfoSql = new String(
            'SELECT
                u.user_id,
                u.fields,
                u.programming_languages,
                u.tools,
                u.years_of_experience,
                u.future_plans,
                u.description
            FROM user_info AS u
            WHERE u.user_id = :user_id');


        $params = new Parameters();
        $params->attach(':user_id', (int)$id, \PDO::PARAM_INT);

        $userQuery = new Query($userInfoSql, array($params), 'fetch', \PDO::FETCH_ASSOC);

        $result = $qh->prepare(new Select($userQuery))->bind()->execute()->getResult();

        $userInfo = $result[0];
        $userInfo['permittions'] = $this->getUserRolesById($id)[0];

        return $userInfo;
    }

    public function getUserRolesById($userId) {
        $qh = new QueryHolder($this->connection);

        $roleSql = new String(
            'SELECT
                r.role
            FROM roles AS r
            WHERE r.user_id = :user_id'
        );

        $params = new Parameters();
        $params->attach(':user_id', (int)$userId, \PDO::PARAM_INT);

        $roleQuery = new Query($roleSql, array($params), 'fetchAll', \PDO::FETCH_COLUMN);

        $result = $qh->prepare(new Select($roleQuery))->bind()->execute()->getResult();

        return $result;
    }

    public function getUsersById(array $userIds) {
        $qh = new QueryHolder($this->connection);

        $ids = implode(',', $userIds);

        $userSql = new String('
            SELECT
               u.user_id,
               u.username,
               u.name,
               u.lastname,
               u.logged
            FROM users AS u
            WHERE IN( _in_ )
        ');

        $parameters = new Parameters();
        $parameters->attach('_in_', $userIds, \PDO::PARAM_INT);

        $query = new Query($userSql, array($parameters), 'fetchAll', \PDO::FETCH_ASSOC);
    }

    public function getAssignedUsersByTestId($testControlId) {
        $qh = new QueryHolder($this->connection);

        $atSql = new String(
            'SELECT
                at.user_id
            FROM restricted_tests AS at
            WHERE at.test_control_id = :test_control_id'
        );

        $params = new Parameters();
        $params->attach(':test_control_id', (int)$testControlId, \PDO::PARAM_INT);

        $atQuery = new Query($atSql, array($params), 'fetchAll', \PDO::FETCH_COLUMN);

        $result = $qh->prepare(new Select($atQuery))->bind()->execute()->getResult();

        return $result[0];
    }

    public function getUserByUsername($username) {
        $qh = new QueryHolder($this->connection);

        $userSql = new String('
            SELECT u.user_id FROM users AS u WHERE u.username = :username
        ');

        $parameters = new Parameters();
        $parameters->attach(':username', $username, \PDO::PARAM_STR);

        $userQuery = new Query($userSql, array($parameters));

        $result = $qh->prepare(new Select($userQuery))->bind()->execute()->getResult();

        if(empty($result[0]) OR $result[0] === null) {
            return null;
        }

        return true;
    }

    public function getPaginatedUsers($from, $to) {
        $qh = new QueryHolder($this->connection);

        $userSql = new String(
            'SELECT
                u.user_id,
                u.username,
                u.name,
                u.lastname,
                u.logged
            FROM users AS u
            WHERE u.user_id BETWEEN :from AND :to');


        $param = new Parameters();
        $param->attach(':from', (int)$from, \PDO::PARAM_INT);
        $param->attach(':to', (int)$to, \PDO::PARAM_INT);


        $userQuery = new Query($userSql, array($param), 'fetchAll', \PDO::FETCH_ASSOC);

        $result = $qh->prepare(new Select($userQuery))->bind()->execute()->getResult();

        return $result[0];
    }
} 