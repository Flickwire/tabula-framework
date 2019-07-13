<?php
namespace Tabula;

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
            //TODO: Handle 404 Better
            header("HTTP/1.0 404 Not Found");
            echo "<html><head><title>File Not Found</title></head><body><h1>404 - File Not Found<h1></body></html>";
        }
    }
}