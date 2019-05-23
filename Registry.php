<?php
namespace Tabula;

class Registry {
    private $data;

    public function __construct(){
        $this->data = array();
    }

    /**
     * Allow magic access to registry values
     * 
     * @author Skye
     */
    public function __call($name, $arguments){
        $method = \substr($name,0,3);
        $key = \str_tokenize(\substr($name,3));
        switch($method){
            case 'has':
                if (\array_key_exists($key,$this->data)){
                    return true;
                }
                return false;
            case 'get':
                if (\array_key_exists($key,$this->data)){
                    return $this->data[$key];
                }
                return null;
            case 'set':
                if(\count($arguments) === 1){
                    $this->data[$key] = $arguments[0];
                } else {
                    throw new \ArgumentCountError("Exactly one argument expected to set registry value");
                }
            default:
                throw new \Exception("Registry does not support the method $name");
        }
    }
}