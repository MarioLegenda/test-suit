<?php

namespace App\ToolsBundle\Repositories\Scenario\Scenarious;

use App\ToolsBundle\Repositories\Query\Connection;
use App\ToolsBundle\Repositories\Query\Parameters\Parameters;
use App\ToolsBundle\Repositories\Query\Query;
use App\ToolsBundle\Repositories\Query\QueryHolder;
use App\ToolsBundle\Repositories\Query\Statement\GenericExecution;
use App\ToolsBundle\Repositories\Scenario\Contracts\ScenarioInterface;

use StrongType\String;

class PublicToRestrictedScenario implements ScenarioInterface
{
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function execute(Connection $connection) {
        $qh = new QueryHolder($connection);

        $testControlSql = new String('
                UPDATE test_control SET test_name = :test_name,
                                        remarks = :remarks,
                                        visibility = :visibility
                WHERE test_control_id = :test_control_id
            ');

        $deletePublicSql = new String('
            DELETE FROM public_tests WHERE test_control_id = :test_control_id
        ');

        $atInsertSql = new String('
            INSERT INTO restricted_tests (user_id, test_control_id)
            VALUES(:user_id, :test_control_id)
        ');

        $tcParams = new Parameters();
        $tcParams->attach(':test_name', $this->data['test_name'], \PDO::PARAM_STR);
        $tcParams->attach(':remarks', $this->data['remarks'], \PDO::PARAM_STR);
        $tcParams->attach(':test_control_id', $this->data['test_control_id'], \PDO::PARAM_INT);
        $tcParams->attach(':visibility', 'restricted', \PDO::PARAM_STR);

        $deleteParam = new Parameters();
        $deleteParam->attach(':test_control_id', $this->data['test_control_id'], \PDO::PARAM_INT);

        $insertParams = array();
        $userIds = $this->data['test_solvers'];

        foreach($userIds as $userId) {
            $param = new Parameters();
            $param->attach(':user_id', $userId, \PDO::PARAM_INT);
            $param->attach(':test_control_id', $this->data['test_control_id'], \PDO::PARAM_INT);

            $insertParams[] = $param;
        }

        $deleteQuery = new Query($deletePublicSql, array($deleteParam));
        $insertQuery = new Query($atInsertSql, $insertParams);
        $tcUpdateQuery = new Query($testControlSql, array($tcParams));

        $qh->prepare(new GenericExecution($tcUpdateQuery, $deleteQuery, $insertQuery))->bind()->execute();
    }
} 