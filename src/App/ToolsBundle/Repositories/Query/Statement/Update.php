<?php
/**
 * Created by PhpStorm.
 * User: Mario
 * Date: 22.4.2015.
 * Time: 14:08
 */

namespace App\ToolsBundle\Repositories\Query\Statement;


use App\ToolsBundle\Repositories\Query\Statement\Contracts\TransactionInterface;
use App\ToolsBundle\Repositories\Query\Statement\Contracts\UpdateStatementInterface;
use App\ToolsBundle\Repositories\Query\Exception\QueryException;

class Update extends Statement implements UpdateStatementInterface, TransactionInterface
{
    private $rowsAffected;

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

                    $this->rowsAffected = $statement->rowCount();
                }

                $this->queryStorage->next();
            }

            $conn->commit();
        }
        catch(\PDOException $e) {
            $conn->rollBack();
            throw new QueryException(get_class($this) . ': Forwarded \PDOException in Update::execute(). Could not commit transaction with PDO message: ' . $e->getMessage());
        }
    }

    public function getRowsAffected() {
        return $this->rowsAffected;
    }
} 