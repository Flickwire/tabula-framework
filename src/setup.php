<?php

/**
 * Set up database schema
 * and do any other needed setup
 * 
 * @author Skye
 */
function tabula_do_setup(\Tabula\Tabula $tabula, string $dbname){
    $db = $tabula->db;

    $isSetup = current($db->query('SELECT COUNT(*) FROM information_schema.TABLES where (TABLE_SCHEMA=?s) AND (TABLE_NAME=?s)',$dbname,'tb_core')->fetch());

    if(!$isSetup){
        initial_setup($tabula);
    } else {
        //TODO: Check version, then upgrade to latest
    }
}

function initial_setup(\Tabula\Tabula $tabula){
    $db = $tabula->db;

    $db->query('CREATE TABLE tb_core (property NVARCHAR(255) NOT NULL, value NVARCHAR(255) NOT NULL, CONSTRAINT PK PRIMARY KEY (property));');
    $db->query('CREATE TABLE tb_modules (module NVARCHAR(255) NOT NULL, version NVARCHAR(32) NOT NULL, CONSTRAINT PK PRIMARY KEY (module));');
    
    $db->query('INSERT INTO tb_core VALUES ("version","1.0");');
}