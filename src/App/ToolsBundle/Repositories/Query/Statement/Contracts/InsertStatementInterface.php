<?php

namespace App\ToolsBundle\Repositories\Query\Statement\Contracts;


interface InsertStatementInterface
{
    function execute($conn);
} 