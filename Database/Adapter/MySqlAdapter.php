<?php
namespace Tabula\Database\Adapter;

use PDO;
use PDOStatement;

class MySqlAdapter implements AbstractAdapter {
    private $resource;
    private $statement;

    public function __construct(string $host, string $database, string $user, string $password, ?string $port="3306", ?string $charset="utf8mb4"){
        if ($port === null){
            $port = "3306";
        }
        if ($charset === null){
            $charset = "utf8mb4";
        }
        $dsn = "mysql:host=$host;dbname=$database;port=$port;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        $this->resource = new PDO($dsn, $user, $password, $options);
    }

    public function query(): PDOStatement{
        $args = \func_get_args();
        if(count($args) === 0){
            throw new \ArgumentCountError("Expected at least one argument");
        } elseif (count($args) === 1){
            $this->statement = $this->resource->query($args[0]);
            return $this->statement;
        } else {
            $args = \func_get_args();
            $query = $args[0];
            //Parse query to find typed parameters
            $parsedQuery = $this->detectTypes($query);
            $query = $parsedQuery[0];
            $types = $parsedQuery[1];
            if ($this->statement instanceof PDOStatement){
                $this->statement->closeCursor();
            }
            if (!($this->statement instanceof PDOStatement) || $this->statement->queryString != $query){
                $this->statement = $this->resource->prepare($query);
            }
            //Set type for typed parameters
            foreach($args as $i => $arg){
                if ($i === 0) continue;
                switch($types[$i-1]){
                    case '?i':
                        $type = PDO::PARAM_INT;
                        break;
                    case '?b':
                        $type = PDO::PARAM_BOOL;
                        break;
                    case '?f':
                        $arg = strval($arg);
                    case '?s':
                    default:
                        $type = PDO::PARAM_STR;
                        break;
                }
                $this->statement->bindValue($i, $arg, $type);
            }
            $this->statement->execute();
            return $this->statement;
        }
    }

    private function detectTypes(string $query): array{
        $pattern = '/\?[sibf]?/';
        $indicators = array();
        \preg_match_all($pattern,$query,$indicators);
        $indicators = $indicators[0];
        $query = \preg_replace($pattern,'?',$query);
        return array($query,$indicators);
    }

    public function escape($value): string{
        return $this->resource->quote($value);
    }

    public function close(){
        $this->statement->closeCursor();
        $this->statement = null;
        $this->resource = null;
    }
}