<?php

namespace App\ToolsBundle\Repositories;


use App\ToolsBundle\Repositories\Query\Exception\QueryException;
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
     *     - WorkspaceController::createAnswerAction()
     */
    public function createAnswer($testControlId, $userId) {
        $qh = new QueryHolder($this->connection);

        $answersSql = new String('
            SELECT a.answer_control_id FROM answer_control AS a WHERE test_control_id = :test_control_id AND a.isFinished = 0
        ');

        $params = new Parameters();
        $params->attach(':test_control_id', $testControlId, \PDO::PARAM_INT);

        $answerIdQuery = new Query($answersSql, array($params), 'fetch', \PDO::FETCH_COLUMN);

        $answerId = $qh->prepare(new Select($answerIdQuery))->bind()->execute()->getResult();

        if(empty($answerId[0])) {
            $sql = new String('
                SELECT user_id FROM test_control WHERE test_control_id = :test_control_id
            ');

            $creatorIdQuery = new Query($sql, array($params), 'fetch', \PDO::FETCH_ASSOC);

            $creatorId = $qh->prepare(new Select($creatorIdQuery))->bind()->execute()->getResult();

            try {
                $sql = new String('
                INSERT INTO answer_control (test_control_id, user_that_created, user_that_solves, isFinished, solving_started)
                VALUES(:test_control_id, :user_that_created, :user_that_solves, :isFinished, NOW())
               ');

                $params = new Parameters();
                $params->attach(':test_control_id', $testControlId, \PDO::PARAM_INT);
                $params->attach(':user_that_created', $creatorId[0]['user_id'], \PDO::PARAM_INT);
                $params->attach(':user_that_solves', $userId, \PDO::PARAM_INT);
                $params->attach(':isFinished', 0, \PDO::PARAM_INT);

                $createAnswerQuery = new Query($sql, array($params));
                $qh->prepare(new Insert($createAnswerQuery))->bind()->execute();
            }
            catch(QueryException $e) {
                $this->deleteAnswerOnAtomicFail($testControlId);
                throw new QueryException(get_class($this) . " Forwarded exception in WorkspaceRepository::createAnswer() with message: " . $e->getMessage());
            }
            catch(\Exception $e) {
                $this->deleteAnswerOnAtomicFail($testControlId);
                throw new QueryException(get_class($this) . " Forwarded exception in WorkspaceRepository::createAnswer() with message: " . $e->getMessage());
            }

            try {
                $lastAnswerId = $qh->getStatement()->getLastInsertedId();
                $sql = new String('
                SELECT t.test_id, t.test_serialized FROM tests AS t WHERE t.test_control_id = :test_control_id
                ');

                $params = new Parameters();
                $params->attach(':test_control_id', $testControlId, \PDO::PARAM_INT);

                $query = new Query($sql, array($params));
                $tests = $qh->prepare(new Select($query))->bind()->execute()->getResult();

                $sql = new String('
                    INSERT INTO answers (answer_control_id, test_id, answer_serialized)
                    VALUES(:answer_control_id, :test_id, :answer_serialized)
                ');

                $paramsArray = array();
                foreach($tests[0] as $test) {
                    $param = new Parameters();
                    $param->attach(':test_id', $test['test_id'], \PDO::PARAM_INT);
                    $param->attach(':answer_control_id', $lastAnswerId, \PDO::PARAM_INT);
                    $param->attach(':answer_serialized', $test['test_serialized'], \PDO::PARAM_STR);

                    $paramsArray[] = $param;
                }

                $query = new Query($sql, $paramsArray);

                $qh->prepare(new Insert($query))->bind()->execute();
            }
            catch(QueryException $e) {
                $this->deleteAnswerOnAtomicFail($testControlId, $lastAnswerId);
                throw new QueryException(get_class($this) . " Forwarded exception in WorkspaceRepository::createAnswer() with message: " . $e->getMessage());
            }
            catch(\Exception $e) {
                $this->deleteAnswerOnAtomicFail($testControlId, $lastAnswerId);
                throw new QueryException(get_class($this) . " Forwarded exception in WorkspaceRepository::createAnswer() with message: " . $e->getMessage());
            }
        }

        return true;
    }

    public function saveAnswer($answer) {
        $qh = new QueryHolder($this->connection);

        $answerId = $answer['answer_id'];
        $answerSerialized = $answer['answer_serialized'];

        $updateSql = new String('
            UPDATE answers SET answer_serialized = :answer_serialized WHERE answers_id = :answer_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':answer_serialized', json_encode($answerSerialized), \PDO::PARAM_STR);
        $parameters->attach(':answer_id', $answerId, \PDO::PARAM_INT);

        try {
            $updateQuery = new Query($updateSql, array($parameters));
            $qh->prepare(new Update($updateQuery))->bind()->execute();
        }
        catch(QueryException $e) {
            throw new QueryException(get_class($this) . ": Forwarded exception with message: " . $e->getMessage());
        }
    }

    public function finishSolvingTest($answerControlId) {
        $qh = new QueryHolder($this->connection);

        $finishSql = new String('
            UPDATE answer_control SET isFinished = 1 WHERE answer_control_id = :answer_control_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':answer_control_id', $answerControlId, \PDO::PARAM_INT);

        try {
            $updateQuery = new Query($finishSql, array($parameters));
            $qh->prepare(new Update($updateQuery))->bind()->execute();
        }
        catch(QueryException $e) {
            throw new QueryException(get_class($this) . ": Forwarded exception with message: " . $e->getMessage());
        }
    }

    /**
     * Called by:
     *     - $this::createAnswer()
     */
    private function deleteAnswerOnAtomicFail($testControlId, $answerControlId = null) {
        $qh = new QueryHolder($this->connection);

        $deleteAnswerControlSql = new String('
            DELETE FROM answer_control WHERE test_control_id = :test_control_id
        ');

        $params = new Parameters();
        $params->attach(':test_control_id', $testControlId, \PDO::PARAM_INT);

        $query = new Query($deleteAnswerControlSql, array($params));

        $qh->prepare(new Delete($query))->bind()->execute();

        if($answerControlId !== null) {
            $deleteAnswersSql = new String('
            DELETE FROM answers WHERE answer_control_id = :answer_control_id
            ');

            $params = new Parameters();
            $params->attach(':answer_control_id', $testControlId, \PDO::PARAM_INT);

            $query = new Query($deleteAnswersSql, array($params));

            $qh->prepare(new Delete($query))->bind()->execute();
        }
    }

    /**
     * Called by:
     *     - WorkspaceController:::getSolvingStatus()
     */
    public function getSolvingStatus($testControlId, $userId) {
        $qh = new QueryHolder($this->connection);

        $statusSql = new String('
            SELECT
               ac.isFinished
            FROM answer_control AS ac
            WHERE ac.test_control_id = :test_control_id AND ac.user_that_solves = :user_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':test_control_id', $testControlId, \PDO::PARAM_INT);
        $parameters->attach(':user_id', $userId, \PDO::PARAM_INT);

        $statusQuery = new Query($statusSql, array($parameters), 'fetch', \PDO::FETCH_ASSOC);

        $status = $qh->prepare(new Select($statusQuery))->bind()->execute()->getResult();

        if($status[0] === false OR $status[0]['isFinished'] === 0) {
            return false;
        }

        return true;
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
     *     - WorkspaceController::initialAnswerDataAction()
     */
    public function getInitialAnswerData($testControlId) {
        $qh = new QueryHolder($this->connection);

        $answerControlSql = new String('
              SELECT
                  ac.answer_control_id,
                  tc.test_name
                  FROM test_control AS tc
                  INNER JOIN answer_control AS ac ON ac.test_control_id = tc.test_control_id
                  WHERE tc.test_control_id = :test_control_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':test_control_id', $testControlId, \PDO::PARAM_INT);

        try {
            $acQuery = new Query($answerControlSql, array($parameters), 'fetch', \PDO::FETCH_ASSOC);
            $testName = $qh->prepare(new Select($acQuery))->bind()->execute()->getResult();
        }
        catch(QueryException $e) {
            throw new QueryException(get_class($this) . ": Forwarded exception with message: " . $e->getMessage());
        }

        $answerIdsSql = new String('
            SELECT
              a.answers_id
              FROM answers AS a
              INNER JOIN test_control AS tc
              INNER JOIN answer_control AS ac
              ON ac.test_control_id = :test_control_id
              WHERE ac.answer_control_id = a.answer_control_id AND ac.test_control_id = tc.test_control_id
        ');

        try {
            $acQuery = new Query($answerIdsSql, array($parameters), 'fetchAll', \PDO::FETCH_COLUMN);
            $result = $qh->prepare(new Select($acQuery))->bind()->execute()->getResult();
        }
        catch(QueryException $e) {
            throw new QueryException(get_class($this) . ": Forwarded exception with message: " . $e->getMessage());
        }

        return array(
            'answer_control_id' => $testName[0]['answer_control_id'],
            'test_name' => $testName[0]['test_name'],
            'range' => $result[0]
        );


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
     *     - WorkspaceController::getAnswer()
     */
    public function getAnswerById($answerId) {
        $qh = new QueryHolder($this->connection);

        $answerSql = new String('
            SELECT a.answers_id, a.answer_serialized FROM answers AS a WHERE a.answers_id = :answers_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':answers_id', $answerId, \PDO::PARAM_INT);

        $answerQuery = new Query($answerSql, array($parameters), 'fetch', \PDO::FETCH_ASSOC);

        $result = $qh->prepare(new Select($answerQuery))->bind()->execute()->getResult();

        $result[0]['answer_serialized'] = json_decode($result[0]['answer_serialized']);
        return $result[0];
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

        $deleteAnswerSql = new String('
            DELETE FROM answers WHERE test_id = :test_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':test_id', $testId, \PDO::PARAM_INT);

        $deleteTestQuery = new Query($deleteTestSql, array($parameters));
        $deleteAnswerQuery = new Query($deleteAnswerSql, array($parameters));

        $qh->prepare(new Delete($deleteTestQuery, $deleteAnswerQuery))->bind()->execute();
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

        $testsSql = new String('
            SELECT t.test_id FROM tests AS t WHERE t.test_control_id = :test_control_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':test_control_id', $content['test_control_id'], \PDO::PARAM_INT);

        $testsQuery = new Query($testsSql, array($parameters));

        $tests = $qh->prepare(new Select($testsQuery))->bind()->execute()->getResult();

        if(empty($tests[0])) {
            return false;
        }

        $finishSql = new String('
            UPDATE test_control SET isFinished = :is_finished WHERE test_control_id = :test_control_id
        ');

        $parameters = new Parameters();
        $parameters->attach(':test_control_id', $content['test_control_id'], \PDO::PARAM_INT);
        $parameters->attach(':is_finished', $content['status'], \PDO::PARAM_INT);

        try {
            $updateQuery = new Query($finishSql, array($parameters));
            $qh->prepare(new Update($updateQuery))->bind()->execute();
        }
        catch(QueryException $e) {
            throw new QueryException(get_class($this) . ": Forwarded exception with message: " . $e->getMessage());
        }

        return true;
    }
} 