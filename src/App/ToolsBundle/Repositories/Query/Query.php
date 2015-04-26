<?php

namespace App\ToolsBundle\Repositories\Query;


use App\ToolsBundle\Repositories\Query\Exception\QueryException;
use App\ToolsBundle\Repositories\Query\Parameters\Parameters;
use StrongType\String;

class Query
{
    private $query;
    private $params;
    private $fetchStyle;
    private $method;

    private $rememberLastInserted = false;

    public function __construct(String $sql, array $params, $method = 'fetchAll', $fetchStyle = \PDO::FETCH_ASSOC) {
        if(empty($params)) {
            throw new QueryException(get_class($this) . ': Query::__construct()-> $params cannot be an empty array');
        }

        foreach($params as $param) {
            if( ! $param instanceof Parameters) {
                throw new QueryException(get_class($this) . ': Query::__construct()-> $params has to be an array of App\ToolsBundle\Repositories\Query\Parameters\Parameters objects');
            }
        }

        $this->query = $sql;
        $this->params = $params;
        $this->method = $method;
        $this->fetchStyle = $fetchStyle;
    }

    public function getQuery() {
        return $this->query->toString();
    }

    public function getParameters() {
        return $this->params;
    }

    public function getPDOMethod() {
        return $this->method;
    }

    public function getFetchStyle() {
        return ($this->fetchStyle === null) ? \PDO::FETCH_ASSOC : $this->fetchStyle;
    }

    public function rememberLastInserted() {
        $this->rememberLastInserted = true;
    }

    public function hasToRemember() {
        return $this->rememberLastInserted;
    }
} 