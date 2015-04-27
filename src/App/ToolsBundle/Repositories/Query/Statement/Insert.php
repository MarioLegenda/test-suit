<?php

namespace App\ToolsBundle\Repositories\Query\Statement;


use App\ToolsBundle\Repositories\Query\Statement\Contracts\InsertStatementInterface;
use App\ToolsBundle\Repositories\Query\Exception\QueryException;
use App\ToolsBundle\Repositories\Query\Statement\Contracts\TransactionInterface;

class Insert extends Statement implements InsertStatementInterface, TransactionInterface
{
    private $lastInsertedId = null;

    public function execute($conn) {
        try {
            $this->queryStorage->rewind();
            while($this->queryStorage->valid()) {
                $query = $this->queryStorage->current();
                $statement = $this->queryStorage->offsetGet($query);
                $parameters = $query->getParameters();

                foreach($parameters as $parameter) {
                    $parameter->rewind();
                    while($parameter->valid()) {
                        $param = $parameter->current();
                        $statement->bindValue(
                            $param->param(),
                            $param->value(),
                            $param->dataType()
                        );

                        $parameter->next();
                    }

                    $statement->execute();

                    $this->lastInsertedId = $conn->lastInsertId();
                }

                $this->queryStorage->next();
            }

            $conn->commit();
        }
        catch(\PDOException $e) {
            $conn->rollBack();
            throw new QueryException(get_class($this) . ': Forwarded \PDOException in Insert::execute(). Could not commit transaction with PDO message: ' . $e->getMessage());
        }
    }

    public function getLastInsertedId() {
        return $this->lastInsertedId;
    }
} 