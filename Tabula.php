<?php
namespace Tabula;

/**
 * The main Tabula object, use this to get the registry/router etc
 * 
 * @author Skye
 */
class Tabula {
    public $registry;
    public $router;
    public $config;
    public $db;

    public function __construct() {
        $this->registry = new Registry($this);
        $this->router = new Router($this);
        $this->config = new Config(\file_get_contents($this->registry->getFsBase() . 'config.json'));

        if(!isset($this->config->database)){
            throw new \Exception("No database details found in config");
        }
        $dbconf = $this->config->database;
        switch ($dbconf['type']){
            case "mysql":
                $this->db = new Database\Adapter\MySqlAdapter($dbconf['host'], $dbconf['database'], $dbconf['user'], $dbconf['password'], (isset($dbconf['port']) ? $dbconf['port'] : null), (isset($dbconf['charset']) ? $dbconf['charset'] : null));
                break;
            default:
                throw new \Exception("Unsupported database type {$dbconf['type']}");
        }

        //Create the request object
        $this->registry->setRequest(new Request($this));
    }
}