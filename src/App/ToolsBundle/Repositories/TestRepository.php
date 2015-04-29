<?php

namespace App\ToolsBundle\Repositories;

use App\ToolsBundle\Helpers\Observer\Observables\PermissionObservable;
use App\ToolsBundle\Helpers\Observer\Observers\PermissionObserver;
use App\ToolsBundle\Repositories\Query\Exception\QueryException;
use App\ToolsBundle\Repositories\Query\QueryHolder;

use App\ToolsBundle\Repositories\Query\Parameters\Parameters;
use App\ToolsBundle\Repositories\Query\Query;

use App\ToolsBundle\Repositories\Query\Statement\Delete;
use App\ToolsBundle\Repositories\Query\Statement\Select;
use App\ToolsBundle\Repositories\Query\Statement\Update;
use App\ToolsBundle\Repositories\Scenario\Condition;
use App\ToolsBundle\Repositories\Scenario\ScenarioFactory;
use StrongType\String;

use RandomLib;

class TestRepository extends Repository
{
    /**
     * Controller: TestController::createTestAction()
     */
    public function createTest(array $testInformation) {
        $scenario = ScenarioFactory::init()
            ->condition(new Condition($testInformation['scenario']))
            ->createScenario();

        $scenario->execute($this->connection);
    }

    /**
     * Controller: TestController::modifyTestAction()
     */
    public function updateTestById($testControlId, array $testArray) {
        $qh = new QueryHolder($this->connection);

        $testControlUpdateSql = new String('
            UPDATE test_control SET test_name = :test_name, remarks = :remarks WHERE test_control_id = :test_control_id
        ');

        $assignedTestUpdateSql = new String('
            UPDATE assigned_tests SET user_id = :user_id, public_test = :public_test WHERE test_control_id = :test_control_id
        ');

        $tcUpdateParams = new Parameters();
        $tcUpdateParams->attach(':test_name', $testArray['test_name'], \PDO::PARAM_STR);
        $tcUpdateParams->attach(':remarks', $testArray['remarks'], \PDO::PARAM_STR);
        $tcUpdateParams->attach(':test_control_id', $testControlId, \PDO::PARAM_STR);

        $paramsArray = array();
        $atUpdateParams = new Parameters();
        $atUpdateParams->initialize(function($context) use ($testArray, $testControlId, &$paramsArray) {
            if($testArray['test_solvers'] === 'public') {
                $context->attach(':user_id', null, \PDO::PARAM_NULL);
                $context->attach(':test_control_id', $testControlId, \PDO::PARAM_INT);
                $context->attach(':public_test', 1, \PDO::PARAM_INT);
                return;
            }

            if(is_array($testArray['test_solvers'])) {
                $ids = $testArray['test_solvers'];

                foreach($ids as $id) {
                    $p = new Parameters();
                    $p->attach(':user_id', $id, \PDO::PARAM_INT);
                    $p->attach(':test_control_id', $testControlId, \PDO::PARAM_INT);
                    $p->attach(':public_test', 0, \PDO::PARAM_INT);

                    $paramsArray[] = $p;
                }
            }
        });

        try {
            $tcQuery = new Query($testControlUpdateSql, array($tcUpdateParams));
            $atQuery = new Query($assignedTestUpdateSql, (empty($paramsArray)) ? array($atUpdateParams) : $paramsArray);
            $qh->prepare(new Update($tcQuery, $atQuery))->bind()->execute();
        }
        catch(\PDOException $e) {
            throw new QueryException(get_class($this) . ': Forwarded exception from TestRepository::modifyTestById()-> Could not successfully update test with atomic update with message: ' . $e->getMessage());
        }
        catch(QueryException $e) {
            throw new QueryException(get_class($this) . ': Forwarded exception from TestRepository::modifyTestById()-> Could not successfully update test with atomic update with message: ' . $e->getMessage());
        }
    }

    /**
     * Controller: TestController::deleteTestAction()
     */
    public function deleteTestSuitById($testControlId) {
        $qh = new QueryHolder($this->connection);


        $assignedTestDltSql = new String('
            DELETE FROM restricted_tests WHERE test_control_id = :test_control_id
        ');

        $testsDltSql = new String('
            DELETE FROM tests WHERE test_control_id = :test_control_id
        ');

        $testsDltSql = new String('
            DELETE FROM public_tests WHERE test_control_id = :test_control_id
        ');

        $testControlDltSql = new String('
            DELETE FROM test_control WHERE test_control_id = :test_control_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':test_control_id', $testControlId, \PDO::PARAM_INT);

        try {
            $testsQuery = new Query($testsDltSql, array($parameters));
            $atQuery = new Query($assignedTestDltSql, array($parameters));
            $tcQuery = new Query($testControlDltSql, array($parameters));

            $qh->prepare(new Delete($atQuery,  $testsQuery, $tcQuery))->bind()->execute();
        }
        catch(QueryException $e) {
            throw new QueryException('An unsuspected error occured in TestRepository::deleteTestSuitById() with message: ' . $e->getMessage());
        }
    }

    /**
     *  Controller:: TestController::getPermissionTypeAction()
     */
    public function getPermissionTypeByTestId($testControlId) {
        $qh = new QueryHolder($this->connection);

        $assignedTestsSql = new String('
            SELECT
                at.assigned_test_id,
                at.user_id,
                at.test_control_id,
                at.public_test
            FROM assigned_tests AS at
            WHERE at.test_control_id = :test_control_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':test_control_id', $testControlId, \PDO::PARAM_INT);

        $atQuery = new Query($assignedTestsSql, array($parameters));

        $result = $qh->prepare(new Select($atQuery))->bind()->execute()->getResult();

        $observable = new PermissionObservable();
        $observable->attach(new PermissionObserver($result[0]));
        $observable->notify();

        return array(
            'permission' => $observable->getStatus()
        );
    }

    /**
     *  Controller: TestController::getTestsListingAction()
     */
    public function getTestsList($userId) {
        $qh = new QueryHolder($this->connection);

        $userSql = new String('
            SELECT
	          t.test_control_id,
              t.test_name,
              t.visibility AS permission,
              t.isFinished,
              t.remarks,
              DATE_FORMAT(t.created, \'%M %d, %Y\') AS created,
              u.username,
              u.name,
              u.lastname
            FROM test_control AS t
            INNER JOIN users AS u
            ON t.user_id = :user_id
            WHERE t.user_id = u.user_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':user_id', $userId, \PDO::PARAM_INT);

        $testQuery = new Query($userSql, array($parameters));
        $result = $qh
            ->prepare(new Select($testQuery))
            ->bind()
            ->execute()
            ->getResult();

        if(empty($result[0])) {
            return array();
        }

        $tests = array();
        foreach($result[0] as $res) {
            $temp = array();

            $temp['test_id'] = $res['test_control_id'];
            $temp['test_name'] = $res['test_name'];
            $temp['permission'] = $res['permission'];
            $temp['user']['username'] = $res['username'];
            $temp['user']['name'] = $res['name'];
            $temp['user']['lastname'] = $res['lastname'];
            $temp['finished'] = $res['isFinished'];
            $temp['remarks'] = $res['remarks'];
            $temp['created'] = $res['created'];

            $tests[] = $temp;
        }

        return $tests;
    }

    /**
     *  Controller: TestController::getPermittedTestUsersAction()
     */
    public function getRestrictedTestsByTestControlId($testControlId) {
        $qh = new QueryHolder($this->connection);

        $restrictedUsersSql = new String('
            SELECT
                u.name,
                u.lastname,
                u.username,
                u.logged
            FROM users AS u
            INNER JOIN restricted_tests AS t
            ON t.test_control_id = :test_control_id
            WHERE u.user_id = t.user_id
        ');

        $params = new Parameters();
        $params->attach(':test_control_id', $testControlId, \PDO::PARAM_INT);

        $query = new Query($restrictedUsersSql, array($params));

        $result = $qh->prepare(new Select($query))->bind()->execute()->getResult();

        if(empty($result[0])) {
            return array();
        }

        return $result[0];
    }

    public function getPermittedTestsByUserId($userId) {
        $qh = new QueryHolder($this->connection);

        $permittedTestsSql = new String('
          SELECT DISTINCT
            t.test_control_id,
            t.visibility,
            t.test_name,
            t.remarks,
            DATE_FORMAT(t.created, \'%M %d, %Y\') AS created,
            u.username,
            u.name,
            u.lastname
          FROM test_control AS t
          INNER JOIN restricted_tests AS rt
          INNER JOIN public_tests AS p
          INNER JOIN users AS u
          ON rt.user_id = :user_id
          WHERE rt.user_id = u.user_id AND t.isFinished = 1
        ');

        $params = new Parameters();
        $params->attach(':user_id', $userId, \PDO::PARAM_INT);

        $query = new Query($permittedTestsSql, array($params));

        $result = $qh->prepare(new Select($query))->bind()->execute()->getResult();

        if(empty($result[0])) {
            return array();
        }

        return $result[0];
    }
} 