<?php
namespace Tabula;

/**
 * The main Tabula object, use this to get the registry/router etc
 * 
 * @author Skye
 */
class Tabula {
    public $registry;
    public $router;

    public function __construct() {
        $this->registry = new Registry($this);
        $this->router = new Router($this);

        //Create the request object
        $this->registry->setRequest(new Request($this));
    }
}