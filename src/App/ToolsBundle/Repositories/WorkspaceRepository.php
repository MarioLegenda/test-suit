<?php

namespace App\ToolsBundle\Repositories;


use App\ToolsBundle\Repositories\Query\Parameters\Parameters;
use App\ToolsBundle\Repositories\Query\Query;
use App\ToolsBundle\Repositories\Query\QueryHolder;
use App\ToolsBundle\Repositories\Query\Statement\Delete;
use App\ToolsBundle\Repositories\Query\Statement\Insert;
use App\ToolsBundle\Repositories\Query\Statement\Select;
use App\ToolsBundle\Repositories\Query\Statement\Update;
use StrongType\String;

class WorkspaceRepository extends Repository
{
    /**
     * Called by:
     *     - WorkspaceController::saveTestAction()
     */
    public function saveTest(array $testData) {
        $qh = new QueryHolder($this->connection);

        $testSql = new String('
            INSERT INTO tests (test_control_id, test_serialized) VALUES(:test_control_id, :test_serialized)
        ');

        $parameters = new Parameters();
        $parameters->attach(':test_control_id', $testData['test_control_id'], \PDO::PARAM_INT);
        $parameters->attach(':test_serialized', $testData['data'], \PDO::PARAM_STR);

        $testQuery = new Query($testSql, array($parameters));

        $qh->prepare(new Insert($testQuery))->bind()->execute();
    }

    /**
     * Called by:
     *     - WorkspaceController::workspaceDataAction()
     */
    public function getInitialWorkspaceData($testControlId) {

        $testControlSql = new String('
            SELECT
              tc.test_name,
              IFNULL(MIN(t.test_id), 0) AS min,
              IFNULL(MAX(t.test_id), 0) AS max
            FROM test_control AS tc
            INNER JOIN tests AS t
            WHERE tc.test_control_id = :test_control_id && tc.test_control_id = t.test_control_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':test_control_id', $testControlId, \PDO::PARAM_INT);

        $testControlQuery = new Query($testControlSql, array($parameters));

        $qh = new QueryHolder($this->connection);

        $result = $qh->prepare(new Select($testControlQuery))->bind()->execute()->getResult();

        if(empty($result[0])) {
            return array();
        }

        return $result[0];
    }

    /**
     * Called by:
     *     - WorkspaceController::getTestAction()
     */
    public function getTestById($testId, $testControlId) {
        $qh = new QueryHolder($this->connection);

        $testSql = new String('
            SELECT
                t.test_id,
                t.test_control_id,
                t.test_serialized
            FROM tests AS t WHERE t.test_id = :test_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':test_id', $testId, \PDO::PARAM_INT);

        $testQuery = new Query($testSql, array($parameters));

        $result = $qh->prepare(new Select($testQuery))->bind()->execute()->getResult();

        if(empty($result[0])) {
            return null;
        }

        if($testControlId === null) {
            return array(
                'test' => $result[0][0],
                'range' => array()
            );
        }

        $testSql = new String('
            SELECT
                t.test_id
            FROM tests AS t
            WHERE t.test_control_id = :test_control_id
        ');

        $parameters->clear();
        $parameters->attach('test_control_id', $testControlId, \PDO::PARAM_INT);

        $testQuery = new Query($testSql, array($parameters), 'fetchAll', \PDO::FETCH_COLUMN);

        $range = $qh->prepare(new Select($testQuery))->bind()->execute()->getResult();

        return array(
            'test' => $result[0][0],
            'range' => (empty($range[0])) ? array() : $range[0]
        );
    }

    /**
     * Called by:
     *     - WorkspaceController::deleteQuestionAction()
     */
    public function deleteQuestionById($testId) {
        $qh = new QueryHolder($this->connection);

        $deleteTestSql = new String('
            DELETE FROM tests WHERE test_id = :test_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':test_id', $testId, \PDO::PARAM_INT);

        $deleteTestQuery = new Query($deleteTestSql, array($parameters));

        $qh->prepare(new Delete($deleteTestQuery))->bind()->execute();
    }

    /**
     * Called by:
     *     - WorkspaceController::updateTestAction()
     */
    public function modifyTestById($testId, $testData) {
        $qh = new QueryHolder($this->connection);

        $updateTestSql = new String('
            UPDATE tests SET test_serialized = :test_data WHERE test_id = :test_id
        ');

        $parameters = new Parameters();
        $parameters->attach('test_id', $testId, \PDO::PARAM_INT);
        $parameters->attach('test_data', json_encode($testData), \PDO::PARAM_STR);

        $updateQuery = new Query($updateTestSql, array($parameters));

        $qh->prepare(new Update($updateQuery))->bind()->execute();
    }

    /**
     * Called by:
     *     - WorkspaceController::finishTestAction()
     */
    public function finishTest(array $content) {
        $qh = new QueryHolder($this->connection);

        $finishSql = new String('
            UPDATE test_control SET isFinished = :is_finished WHERE test_control_id = :test_control_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':test_control_id', $content['test_control_id'], \PDO::PARAM_INT);
        $parameters->attach(':is_finished', $content['status'], \PDO::PARAM_INT);

        $updateQuery = new Query($finishSql, array($parameters));

        $qh->prepare(new Update($updateQuery))->bind()->execute();
    }
} 