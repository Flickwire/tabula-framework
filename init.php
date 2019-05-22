<?php
define('DS',DIRECTORY_SEPARATOR);
//Set up Tabula Autoloader
require_once('autoloader.php');

//Initialise Tabula and hand off to the router
$tabula = \Tabula\Tabula::getInstance();
//Load the module autoloader
new \Tabula\Module\Autoloader();
$tabula->doRouting();