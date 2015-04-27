<?php

namespace App\ToolsBundle\Repositories\Query;

use App\ToolsBundle\Repositories\Query\Statement\Statement;

class QueryHolder
{
    private $conn;

    private $statement;

    public function __construct(Connection $conn) {
        $this->conn = $conn;
    }

    public function prepare(Statement $statement) {
        $statement->prepare($this->conn->getConnection());
        $this->statement = $statement;

        return $this;
    }

    public function getStatement() {
        return $this->statement;
    }

    public function execute() {
        $this->statement->execute($this->conn->getConnection());
        return $this;
    }

    public function bind() {
        return $this;
    }

    public function getResult() {
        return $this->statement->getResult();
    }
} 