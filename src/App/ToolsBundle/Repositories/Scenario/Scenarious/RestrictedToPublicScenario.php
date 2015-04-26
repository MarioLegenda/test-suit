<?php

namespace App\ToolsBundle\Repositories\Scenario\Scenarious;

use App\ToolsBundle\Repositories\Query\Connection;
use App\ToolsBundle\Repositories\Query\Exception\QueryException;
use App\ToolsBundle\Repositories\Query\Parameters\Parameters;
use App\ToolsBundle\Repositories\Query\Query;
use App\ToolsBundle\Repositories\Query\QueryHolder;
use App\ToolsBundle\Repositories\Query\Statement\Delete;
use App\ToolsBundle\Repositories\Query\Statement\Insert;
use App\ToolsBundle\Repositories\Query\Statement\Update;
use App\ToolsBundle\Repositories\Scenario\Contracts\ScenarioInterface;

use StrongType\String;


class RestrictedToPublicScenario implements ScenarioInterface
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
                                        visibility = :permission
                WHERE test_control_id = :test_control_id
            ');


        $deleteRestrictedTestsSql = new String('
            DELETE FROM restricted_tests WHERE test_control_id = :test_control_id
        ');

        $publicTestInsertSql = new String('
            INSERT INTO public_tests (test_control_id) VALUES(:test_control_id)
        ');

        $testControlParam = new Parameters();
        $testControlParam->attach(':test_control_id', $this->data['test_control_id'], \PDO::PARAM_INT);

        $tcParams = new Parameters();
        $tcParams->attach(':test_name', $this->data['test_name'], \PDO::PARAM_STR);
        $tcParams->attach(':remarks', $this->data['remarks'], \PDO::PARAM_STR);
        $tcParams->attach(':permission', 'public', \PDO::PARAM_STR);
        $tcParams->attach(':test_control_id', $this->data['test_control_id'], \PDO::PARAM_INT);

        $tcUpdateQuery = new Query($testControlSql, array($tcParams));
        $deleteQuery = new Query($deleteRestrictedTestsSql, array($testControlParam));
        $publicInsertQuery = new Query($publicTestInsertSql, array($testControlParam));

        $qh->prepare(new Update($tcUpdateQuery, $deleteQuery, $publicInsertQuery))->bind()->execute();
    }
} 