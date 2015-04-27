<?php

namespace App\ToolsBundle\Repositories;

use App\ToolsBundle\Repositories\Query\Connection;

abstract class Repository
{
    protected $connection;

    public function __construct(Connection $conn) {
        $this->connection = $conn;
    }
} 