<?php

namespace App\ToolsBundle\Repositories\Query\Statement;


use App\ToolsBundle\Repositories\Query\Parameters;
use App\ToolsBundle\Repositories\Query\Statement\Contracts\SelectStatementInterface;

class Select extends Statement implements SelectStatementInterface
{
    public function execute($conn) {
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
    }

    public function getResult() {
        $results = array();
        $this->queryStorage->rewind();
        while($this->queryStorage->valid()) {
            $query = $this->queryStorage->current();
            $statement = $this->queryStorage->offsetGet($query);

            $method = $query->getPDOMethod();
            $fetchStyle = $query->getFetchStyle();

            $results[] = $statement->$method($fetchStyle);

            $this->queryStorage->next();
        }

        return $results;
    }
} 