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
        $this->registry->setDebug(isset($this->config->debug) && $this->config->debug);

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

        //Register site name
        $this->registry->setSiteName($this->config->name);

        // Do Database setup
        \tabula_do_setup($this,$dbconf['database']);

        //Create the request object
        $this->registry->setRequest(new Request($this));

        //Set up renderer
        $this->renderer = new Renderer($this);
        $this->renderer->registerTemplateDir(__DIR__.DS.'templates');
        $this->renderer->registerScriptDir(__DIR__.DS.'scripts');

        //Load admin area
        $this->registry->setAdminPanel(new Admin($this));

        //Load auth handler
        $this->registry->setAuthHandler(new Auth($this));

        //Register modules
        $this->registry->setModuleRegistrar(new Registrar($this));

        //Last step, hand off to router
        $this->router->doRouting();
    }

    //Helper functions from here on

    /**
     * Redirect to the specified uri
     */
    public function redirect(string $uri){
        header("Location: $uri",true,303);
        die();
    }
}