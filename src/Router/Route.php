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

    public function __construct(string $path, object $controller, string $method){
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
    public function isMatch(string $url): bool {
        return 0 === strncmp($url,$this->path,strlen($this->path));
    }

    /**
     * Execute the method which handles this route
     * 
     * @author Skye
     */
    public function run(){
        if (!is_object($this->controller)){
            throw new \ErrorException("Invalid controller");
        }
        if (!method_exists($this->controller, $this->method)){
            throw new \ErrorException("Controller has no method {$this->method}");
        }
        return call_user_func_array([$this->controller,$this->method],[]);
    }
}