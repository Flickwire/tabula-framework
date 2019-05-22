<?php
namespace Tabula;

/**
 * The main Tabula object, use this to get the registry/router etc
 * 
 * @author Skye
 */
class Tabula {
    private static $instance;

    public $registry;
    public $router;

    private function __construct() {
        $this->registry = new Registry();
        $this->router = new Router();
    }

    /**
     * Get the tabula instance to pull out things like the registry or router
     * 
     * @author Skye
     */
    public static function getInstance() {
        if (!Tabula::$instance instanceof Tabula){
            Tabula::$instance = new Tabula();
        }
        return Tabula::$instance;
    }

    /**
     * Route the current URL to the correct route
     * 
     * @author Skye
     */
    public function doRouting() {
        $url = $this->registry->getRequest()->getUri;//$_SERVER[REQUEST_URI]; //TODO: Replace this with a registry call, once the registry exists
        $route = $this->router->resolve($url);
        if ($route instanceof Router\Route){
            $route->run();
        } else {
            //TODO: Handle 404
        }
    }
}