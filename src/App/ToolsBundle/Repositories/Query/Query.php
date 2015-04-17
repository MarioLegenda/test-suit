<?php

namespace App\ToolsBundle\Repositories\Query;


use StrongType\String;

class Query
{
    private $query;

    public function __construct(String $sql) {
        $this->query = $sql;
    }

    public function getQuery() {
        return $this->query->toString();
    }
} 