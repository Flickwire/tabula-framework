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
        echo "Called registry method $name<br>";
        if (\strncmp($name,'get',3) === 0){
            $key = \str_tokenize(\substr($name,3));
            echo "Checking for array key $key";
            if (\array_key_exists($key,$this->data)){
                return $this->data[$key];
            }
            return null;
        }
    }
}