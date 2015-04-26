<?php

namespace App\ToolsBundle\Repositories\Query;


use App\ToolsBundle\Repositories\Exceptions\RepositoryException;
use RCE\Builder\Builder;
use RCE\ContentEval;
use RCE\Filters\BeString;
use RCE\Filters\Exist;

class Connection
{
    private $conn;

    public function __construct(array $parameters) {
        $builder = new Builder($parameters);

        $builder->build(
            $builder->expr()->hasTo(new Exist('driver'), new BeString('driver')),
            $builder->expr()->hasTo(new Exist('dbname'), new BeString('dbname')),
            $builder->expr()->hasTo(new Exist('host'), new BeString('host')),
            $builder->expr()->hasTo(new Exist('user'), new BeString('user')),
            $builder->expr()->hasTo(new Exist('password'), new BeString('password')),
            $builder->expr()->hasTo(new Exist('persistant'))
        );

        if( ! ContentEval::builder($builder)->isValid()) {
            throw new RepositoryException(get_class($this) . ' Connection object does not have all the neccessary dns properties to establish a connection');
        }

        $this->establishConnection($parameters);
    }

    private function establishConnection(array $parameters) {
        $driver = $parameters['driver'];
        $host = $parameters['host'];
        $dbName = $parameters['dbname'];
        $user = $parameters['user'];
        $password = $parameters['password'];
        $persistant = $parameters['persistant'];

        try {
            $pdo = new \PDO($driver . ':dbname=' . $dbName . ';host=' . $host, $user, $password, array(
                \PDO::ATTR_PERSISTENT => $persistant,
                \PDO::ATTR_EMULATE_PREPARES => false
            ));

            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $pdo->exec('SET NAMES utf8');

            $this->conn = $pdo;

        } catch (\PDOException $e) {
            echo get_class($this) . ' Connection failed: ' . $e->getMessage();
        }
    }

    public function getConnection() {
        return $this->conn;
    }
} 