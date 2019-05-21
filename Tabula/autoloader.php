<?php

/**
 * Autoloader for Tabula
 * 
 * @author Skye
 * @version 2019-05-21
 */
spl_autoload_register(function($class){
    //Only autoload for the Tabula namespace
    $prefix = 'Tabula\\';
    if (0 !== strncmp($class,$prefix,strlen($prefix))){
        return;
    }

    $baseDir = realpath(__DIR__ . DS . '..');
    $file = $baseDir . DS . str_replace('\\', DS, $class) . '.php';

    if(file_exists($file)){
        require_once($file);
    }
});