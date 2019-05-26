<?php
namespace Tabula;

class Config implements \Serializable{
    private $data;

    private function __construct(){
        $this->data = array();
    }

    public function __get(string $name){
        if ($this->__isset($name)){
            return $this->data[$name];
        }
        return null;
    }

    public function __isset(string $name){
        return isset($this->data[$name]);
    }

    public function serialize(): string{
        return \json_encode($this->data);
    }

    public static function unserialize(string $data): Config{
        $config = new Config();
        $config->data = \json_decode($data);
        return $config;
    }

    public function get(string $name){
        return $this->__get($name);
    }

    public function has(string $name){
        return $this->__isset($name);
    }
}