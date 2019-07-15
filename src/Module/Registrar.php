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
    private $table = 'tb_modules';

    public function __construct(\Tabula\Tabula $tabula){
        $this->modules = array();
        $this->tabula = $tabula;
        $this->db = $this->tabula->db;
        $this->discoverModules();
        $this->installModules();
        $this->registerRoutes();
        $this->preInitModules();
        $this->initModules();
    }

    /**
     * Find all the modules that are installed
     * 
     * @author Skye
     */
    private function discoverModules(): void{
        $tabula = $this->tabula;
        foreach (glob($this->tabula->registry->getModulesDir().'*'.DS.'src'.DS.'registermodule.php') as $moduleFile){
            include_once($moduleFile);
            $this->modules[] = $module;
        }
    }

    /**
     * Install modules, setting up their database tables
     * 
     * @author Skye
     */
    private function installModules(): void{

        //Map installed versions of each module
        $modulesInstalled = $this->db->query("SELECT module, version FROM {$this->table};")->fetchAll();
        $moduleVersions = array();
        foreach ($modulesInstalled as $module) {
            $moduleVersions[$module['module']] = $module['version'];
        }

        //Pass installed version to module's upgrade/install script
        //Will pass empty string if module has not been installed yet
        //TODO: separate update/install new SQL queries to make
        //prepared statements run more efficiently
        foreach ($this->modules as $module){
            $version = isset($moduleVersions[$module->getName()]) ? $moduleVersions[$module->getName()] : "";
            $newVersion = $module->upgrade($version,$this->db);
            if ($newVersion !== $version){
                if ($version !== ""){
                    $this->db->query("UPDATE {$this->table} SET version = ?s WHERE module = ?s;", $newVersion, $module->getName());
                } else {
                    $this->db->query("INSERT INTO {$this->table} (module, version) VALUES (?s, ?s);", $module->getName(), $newVersion);
                }
            }
        }
    }

    /**
     * Register the routes for each module
     * 
     * @author Skye
     */
    private function registerRoutes(): void{
        foreach ($this->modules as $module){
            $module->registerRoutes($this->tabula->router);
        }
    }

    /**
     * Run preinit for each module
     * 
     * @author Skye
     */
    private function preInitModules(): void{
        foreach ($this->modules as $module){
            $module->preInit($this->tabula);
        }
    }

    /**
     * Run init for each module
     * 
     * @author Skye
     */
    private function initModules(): void{
        foreach ($this->modules as $module){
            $module->init();
        }
    }
}