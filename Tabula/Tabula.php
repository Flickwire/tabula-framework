<?php
namespace Tabula;

/**
 * The main Tabula object, use this to get the registry/router etc
 */
class Tabula {
    private static $instance;

    public $registry;
    public $router;

    private function __construct(){
        $this->registry = new Registry();
        $this->router = new Router();
    }

    public static function getInstance() {
        if (!Tabula::$instance instanceof Tabula){
            Tabula::$instance = new Tabula();
        }
        return Tabula::$instance;
    }
}