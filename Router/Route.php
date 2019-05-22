<?php
namespace Tabula\Router;

/**
 * An individual route for the router
 * 
 * @author Skye
 */
class Route {
    private $path;
    private $controller;
    private $method;

    public function __construct(string $path, string $controller, string $method){
        $this->path = $path;
        $this->controller = $controller;
        $this->method = $method;
    }

    /**
     * Match route against url
     * TODO: Better route matching with parameters
     * 
     * @return true if this route can handle the given url
     * 
     * @author Skye
     */
    public function isMatch(string $url): boolean {
        return 0 === strncmp($url,$this->path,strlen($this->path));
    }

    /**
     * Execute the method which handles this route
     * 
     * @author Skye
     */
    public function run(){
        if (!class_exists($this->controller)){
            throw new \ErrorException("Class {$this->controller} does not exist");
        }
        if (!method_exists(new $this->controller, $this->method)){
            throw new \ErrorException("Class {$this->controller} has no method {$this->method}");
        }
        return call_user_func_array([new $this->controller,$this->method],[]);
    }
}