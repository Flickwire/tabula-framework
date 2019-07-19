<?php
namespace Tabula;

use Tabula\Module\Registrar;

define('DS',DIRECTORY_SEPARATOR);

/**
 * The main Tabula object, use this to get the registry/router etc
 * 
 * @author Skye
 */
class Tabula {
    public $registry;
    public $session;
    public $router;
    public $renderer;
    public $db;
    
    private $config;

    public function __construct() {
        //Start the PHP session
        \session_start();

        $this->session = new Session();
        $this->registry = new Registry($this);
        $this->router = new Router($this);
        $this->config = new Config(\file_get_contents($this->registry->getFsBase() . 'config.json'));
        $this->renderer = new Renderer($this);

        $this->renderer->registerTemplateDir(__DIR__.DS.'templates');

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

        // Do Database setup
        \tabula_do_setup($this,$dbconf['database']);

        $this->registry->setDebug(isset($this->config->debug) && $this->config->debug);

        //Create the request object
        $this->registry->setRequest(new Request($this));

        //Register modules
        $this->registry->setModuleRegistrar(new Registrar($this));

        //Last step, hand off to router
        $this->router->doRouting();
    }
}