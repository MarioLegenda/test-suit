<?php

namespace App\ToolsBundle\Repositories\Query;


class Connection
{
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function conn() {
        return $this->conn;
    }
} 