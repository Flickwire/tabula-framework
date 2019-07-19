<?php
namespace Tabula;

use Tabula\Renderer\Page;

/**
 * Tabula Router
 * Handles URL resolution
 * 
 * @author Skye
 */
class Router {
    private $routes;
    private $registry;
    private $tabula;

    public function __construct($tabula){
        $this->tabula = $tabula;
        $this->routes = array();
    }

    /**
     * Register a new route
     * 
     * @author Skye
     */
    public function register(Router\Route $route): void {
        $this->routes[] = $route;
    }

    /**
     * Return a matching route for a given url
     * 
     * @author Skye
     */
    public function resolve(string $url): ?Router\Route {
        foreach ($this->routes as $route){
            if ($route->isMatch($url)){
                return $route;
            }
        }
        return null;
    }
    
    /**
     * Route the current URL to the correct route
     * 
     * @author Skye
     */
    public function doRouting() {
        $url = $this->tabula->registry->getRequest()->getUri();//$_SERVER[REQUEST_URI]; //TODO: Replace this with a registry call, once the registry exists
        $route = $this->resolve($url);
        if ($route instanceof Router\Route){
            $route->run();
        } else {
            $this->do404();
        }
    }

    private function do404() {
        $page = new Page($this->tabula, "errors/404.php");
        $page->render();
    }
}