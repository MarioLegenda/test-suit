<?php

namespace App\ToolsBundle\Repositories\Query\Statement;


use App\ToolsBundle\Repositories\Query\Exception\QueryException;
use App\ToolsBundle\Repositories\Query\Query;
use App\ToolsBundle\Repositories\Query\Statement\Contracts\TransactionInterface;

abstract class Statement
{
    protected $queries = array();
    protected $queryStorage;

    public function __construct() {
        $this->queryStorage = new \SplObjectStorage();
        $queries = func_get_args();

        foreach($queries as $query) {
            if( ! $query instanceof Query) {
                throw new QueryException(get_class($this) . ': Statement::__construct() should receive only App\ToolsBundle\Repositories\Query\Query object');
            }
        }

        $this->queries = $queries;
    }

    public function prepare($conn) {
        try {
            if($this instanceof TransactionInterface) {
                $conn->beginTransaction();
            }

            foreach($this->queries as $query) {
                $prepared = $conn->prepare($query->getQuery());
                $this->queryStorage->attach($query, $prepared);
            }
        }
        catch(\PDOException $e) {
            if($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw new QueryException(get_class($this) . ': Forwarded \PDOException in Statement::prepare(). Could not begin transaction with PDO message: ' . $e->getMessage());
        }
    }
} 