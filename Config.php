<?php
namespace Tabula;

class Config{
    private $data;

    public function __construct(string $data){
        $this->data = \json_decode($data,true);
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

    public function get(string $name){
        return $this->__get($name);
    }

    public function has(string $name){
        return $this->__isset($name);
    }
}