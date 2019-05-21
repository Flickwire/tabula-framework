<?php
namespace Tabula\Module;

/**
 * Autoloader for Tabula modules
 * Tries to resolve classes within any module
 * that has registered itself with Tabula
 * 
 * @author Skye
 */
class Autoloader {
    public function __construct(){
        spl_autoload_register(function($class){
            $registry = \Tabula\Tabula::getInstance()->registry;
            $modulesDir = $registry->getModulesDir();
            foreach ($registry->getModules() as $module){
                $baseDir = realpath($modulesDir . DS . $module->getDirName());
                $file = $baseDir . DS . str_replace('\\', DS, $class) . '.php';
            
                if(file_exists($file)){
                    require_once($file);
                    return;
                }
            }
        });
    }
}