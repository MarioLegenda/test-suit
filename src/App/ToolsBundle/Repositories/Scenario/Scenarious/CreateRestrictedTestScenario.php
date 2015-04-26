<?php

namespace App\ToolsBundle\Repositories\Scenario\Scenarious;


use App\ToolsBundle\Repositories\Query\Connection;
use App\ToolsBundle\Repositories\Query\Exception\QueryException;
use App\ToolsBundle\Repositories\Query\Parameters\Parameters;
use App\ToolsBundle\Repositories\Query\Query;
use App\ToolsBundle\Repositories\Query\QueryHolder;
use App\ToolsBundle\Repositories\Query\Statement\Delete;
use App\ToolsBundle\Repositories\Query\Statement\Insert;
use App\ToolsBundle\Repositories\Scenario\Contracts\ScenarioInterface;

use StrongType\String;
use RandomLib;

class CreateRestrictedTestScenario implements ScenarioInterface
{
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function execute(Connection $connection) {
        $factory = new RandomLib\Factory();
        $generator = $factory->getMediumStrengthGenerator();
        $identifier = $generator->generateString(32);

        $qh = new QueryHolder($connection);

        $testControlSql = new String('
            INSERT INTO test_control (user_id, test_name, identifier, visibility, isFinished, remarks, created)
            VALUES (:user_id, :test_name, :identifier, :visibility, :isFinished, :remarks, NOW())
        ');

        $params = new Parameters();
        $params->attach(':user_id', $this->data['user_id'], \PDO::PARAM_INT);
        $params->attach(':test_name', $this->data['test_name'], \PDO::PARAM_STR);
        $params->attach(':identifier', $identifier, \PDO::PARAM_STR);
        $params->attach(':visibility', 'restricted', \PDO::PARAM_STR);
        $params->attach(':isFinished', 0, \PDO::PARAM_INT);
        $params->attach(':remarks', $this->data['remarks'], \PDO::PARAM_STR);

        $tcQuery = new Query($testControlSql, array($params));

        $qh->prepare(new Insert($tcQuery))->bind()->execute();

        $lastId = $qh->getStatement()->getLastInsertedId();

        $restrictedTestsSql = new String('
               INSERT INTO restricted_tests (user_id, test_control_id)
               VALUES(:user_id, :test_control_id)
            ');

        $paramsArray = array();
        $testSolvers = $this->data['test_solvers'];

        foreach($testSolvers as $userId) {
            $param = new Parameters();
            $param->attach(':test_control_id', $lastId, \PDO::PARAM_INT);
            $param->attach(':user_id', $userId, \PDO::PARAM_INT);

            $paramsArray[] = $param;
        }

        try {
            $rtQuery = new Query($restrictedTestsSql, $paramsArray);

            $qh->prepare(new Insert($rtQuery))->bind()->execute();
        }
        catch(QueryException $e) {
            $deleteTcSql = new String('
                DELETE FROM test_control WHERE test_control_id = :test_control_id
            ');

            $params->clear()
                ->attach(':test_control_id', $lastId, \PDO::PARAM_INT);

            $deleteQuery = new Query($deleteTcSql, array($params));

            $qh->prepare(new Delete($deleteQuery))->bind()->execute();

            throw new QueryException(get_class($this) . ': Forwarded exception with message: ' . $e->getMessage());
        }

    }
} 