<?php
namespace Tabula;

class Config implements \Serializable{
    private $data;

    public function __construct(){
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

    public function __set(string $name, $value){
        $this->data[$name] = $value;
    }

    public function __unset(string $name){
        unset($this->data[$name]);
    }

    public function serialize(): string{
        return \json_encode($this->data);
    }

    public function unserialize(string $data): Config{
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

    public function set(string $name, $value){
        return $this->__set($name, $value);
    }

    public function remove(string $name){
        return $this->__unset($name);
    }
}