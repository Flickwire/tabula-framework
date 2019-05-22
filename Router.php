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

    public function __construct(){
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
            if ($route->matches($url)){
                return $route;
            }
        }
        return null;
    }
}