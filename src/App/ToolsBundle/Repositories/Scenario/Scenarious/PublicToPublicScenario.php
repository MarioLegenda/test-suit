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

class PublicToPublicScenario implements ScenarioInterface
{
    private $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    public function execute(Connection $connection) {
        $qh = new QueryHolder($connection);

        $testControlSql = new String('
                UPDATE test_control SET test_name = :test_name,
                                        remarks = :remarks
                WHERE test_control_id = :test_control_id
            ');

        $tcParams = new Parameters();
        $tcParams->attach(':test_name', $this->data['test_name'], \PDO::PARAM_STR);
        $tcParams->attach(':remarks', $this->data['remarks'], \PDO::PARAM_STR);
        $tcParams->attach(':test_control_id', $this->data['test_control_id'], \PDO::PARAM_INT);

        $tcUpdateQuery = new Query($testControlSql, array($tcParams));

        $qh->prepare(new Update($tcUpdateQuery))->bind()->execute();
    }
} 