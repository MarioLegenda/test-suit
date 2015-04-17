<?php

namespace App\ToolsBundle\Repositories\Query;


use App\ToolsBundle\Repositories\Exceptions\RepositoryException;

class QueryHolder
{
    private $conn;
    private $query;

    private $statement;

    public function __construct(Connection $conn, Query $query) {
        $this->conn = $conn->conn();
        $this->query = $query;
    }

    public function prepare() {
        $this->statement = $this->conn->prepare($this->query->getQuery());
        return $this;
    }

    public function statement() {
        return $this->statement;
    }

    public function bind(Parameters $params) {
        if($params->areEmpty()) {
            throw new RepositoryException(get_class($this) . ': parameters supplied to the QueryHolder::bind() cannot be empty');
        }

        while($params->valid()) {
            $parameter = $params->current();

            $this->statement->bindValue(
                $parameter->param(),
                $parameter->value(),
                $parameter->dataType()
            );

            $params->next();
        }

        return $this;
    }
} 