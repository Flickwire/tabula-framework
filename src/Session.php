<?php
namespace Tabula;

class Session {
    
    /**
     * Allow magic access to session values
     * 
     * @author Skye
     */
    public function __call($name, $arguments){
        $method = \substr($name,0,3);
        $key = $this->str_tokenize(\substr($name,3));
        switch($method){
            case 'has':
                if (\array_key_exists($key,$_SESSION)){
                    return true;
                }
                return false;
            case 'get':
                if (\array_key_exists($key,$_SESSION)){
                    return $_SESSION[$key];
                }
                return null;
            case 'set':
                if(\count($arguments) === 1){
                    $_SESSION[$key] = $arguments[0];
                    return;
                } else {
                    throw new \ArgumentCountError("Exactly one argument expected to set session value");
                }
            default:
                throw new \Exception("Session does not support the method $name");
        }
    }

    private function str_tokenize($input){
        return \strtolower(\ltrim(\preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $input), '_'));
    }
}