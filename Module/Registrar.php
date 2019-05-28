<?php
namespace Tabula\Module;

/**
 * Module Registrar
 * 
 * Seeks out and registers Tabula modules
 * These should reside within the modules directory,
 * and should be described by a module.json file
 * This registrar will run any provided setup
 * scripts, if they exist, and set up routes
 * 
 * @author Skye
 */
class Registrar {

    private $modules;
    private $tabula;
    private $db;
    private $table = 'tabula_modules';

    public function __construct(){
        $this->modules = array();
        $this->db = $this->tabula->db;
    }

    /**
     * Find all the modules that are installed
     * 
     * @author Skye
     */
    private function discoverModules(){

    }

    /**
     * Install modules, setting up their database tables
     * 
     * @author Skye
     */
    private function installModules(){
        //Store these to update the database after installing all modules
        //Allows prepared statements to operate more easily
        $installed = array();
        $updated = array();
        foreach ($this->modules as $module){
            if ($module->installable){
                try {
                    $dbModule = $this->db->query("SELECT `module_id`,`module`,`version` FROM `{$this->table}` WHERE `module` = ?s LIMIT 1",$module->name)->fetch();
                    if (!$dbModule || $dbModule['version'] < $module->version){
                        //TODO: Find and execute install and upgrade scripts
    
                    }
                } catch (\Exception $e) {
                    //TODO: Do something with modules which fail to install or update
                }
            }
        }
        foreach ($installed as $module){
            $this->db->query("INSERT INTO `{$this->table}` (`module`,`version`) VALUES (?s, ?s)",$module['name'],$module['version']);
        }
        foreach ($updated as $module){
            $this->db->query("UPDATE `{$this->table}` SET `version` = ?s WHERE `module` = ?s",$module['version'],$module['name']);
        }
    }

    /**
     * Register the routes for each module
     * 
     * @author Skye
     */
    private function registerRoutes(){
        foreach ($this->modules as $module){
            $module->registerRoutes($this->tabula->router);
        }
    }

}