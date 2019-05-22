<?php
define('DS',DIRECTORY_SEPARATOR);
define('TABULA_BASE',__DIR__);
//Set up Tabula Autoloader
require_once('autoloader.php');
//Load helper functions
require_once('niceties.php');

//Initialise Tabula and hand off to the router
$tabula = \Tabula\Tabula::getInstance();
//Load the module autoloader
new \Tabula\Module\Autoloader();
$tabula->doRouting();