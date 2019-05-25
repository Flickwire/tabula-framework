<?php
namespace Tabula\Database\Adapter;

use PDO;

class MySqlAdapter implements AbstractAdapter {
    private $resource;
    private $statement;

    public function __construct(string $host, string $database, string $user, string $password, string $port="3306", string $charset="utf8mb4"){
        $dsn = "mysql:host=$host;dbname=$database;port=$port;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        $this->resource = new PDO($dsn, $user, $password, $options);
    }

    public function query(){
        $args = \func_get_args();
        if(count($args) === 0){
            throw new \ArgumentCountError("Expected at least one argument");
        } elseif (count($args) === 1){
            $this->statement = $this->resource->query($args[0]);
            return $this->statement;
        } else {
            $args = \func_get_args();
            $query = $args[0];
            if (!($this->statement instanceof \PDOStatement) || $this->statement->queryString != $query){
                if ($this->statement instanceof \PDOStatement){
                    $this->statement->closeCursor();
                }
                $this->statement = $this->resource->prepare($query);
            }
            foreach($args as $i => $arg){
                if ($i === 0) continue;
                $this->statement->bindValue($i, $arg);
            }
            $this->statement->execute();
            return $this->statement;
        }
    }

    public function escape($value){
        return $this->resource->quote($value);
    }

    public function close(){
        $this->statement->closeCursor();
        $this->statement = null;
        $this->resource = null;
    }
}