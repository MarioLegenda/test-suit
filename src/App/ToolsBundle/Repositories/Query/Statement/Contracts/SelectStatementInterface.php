<?php

namespace App\ToolsBundle\Repositories\Query\Statement\Contracts;

interface SelectStatementInterface
{
    function execute($conn);
    function getResult();
} 