<?php

namespace App\ToolsBundle\Repositories\Query\Statement;


use App\ToolsBundle\Repositories\Query\Statement\Contracts\DeleteStatementInterface;
use App\ToolsBundle\Repositories\Query\Statement\Contracts\TransactionInterface;
use App\ToolsBundle\Repositories\Query\Exception\QueryException;

class Delete extends Statement implements DeleteStatementInterface, TransactionInterface
{
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
} 