<?php
namespace Tabula;

class Request {
    private $tabula;

    public function __construct($tabula){
        $this->tabula = $tabula;
    }

    public function has($key){
        return \array_key_exists($_POST[$key]) || \array_key_exists($_GET[$key]);
    }

    public function get($key){
        if (\array_key_exists($_POST[$key])){
            return $_POST[$key];
        }
        if (\array_key_exists($_GET[$key])){
            return $_GET[$key];
        }
        return null;
    }

    /**
     * Get the current page URI,
     * relative to the base url of Tabula
     * 
     * @author Skye
     */
    public function getUri(){
        $base = $this->tabula->registry->getUriBase();
        $requestUri = $_SERVER['REQUEST_URI'];
        if(\strncmp($base,$requestUri,strlen($base)) === 0){
            return \str_replace(DS,'/',\substr($requestUri,\strlen($base))); //Find base path of project
        }
        throw new \Exception("How did you get here? I have no idea where to route you. Go away.");
    }
}