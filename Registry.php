<?php
namespace Tabula;

class Registry {
    private $data;
    private $tabula;

    public function __construct(Tabula $tabula){
        $this->tabula = $tabula;
        $this->data = array();
        $this->prefill();
    }

    /**
     * Fill registry with useful default information
     * 
     * @author Skye
     */
    private function prefill(){
        $docroot = $_SERVER["DOCUMENT_ROOT"];
        $projectbase = getcwd();
        $uribase = ''; //Fallback in case path analysis fails
        if(\strncmp($docroot,$projectbase,strlen($docroot)) === 0){
            $uribase = \str_replace(DS,'/',\substr($projectbase,\strlen($docroot))); //Find base path of project
        }
        $this->setFsBase($projectbase.'/');
        $this->setUriBase($uribase);
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
                    return;
                } else {
                    throw new \ArgumentCountError("Exactly one argument expected to set registry value");
                }
            default:
                throw new \Exception("Registry does not support the method $name");
        }
    }
}