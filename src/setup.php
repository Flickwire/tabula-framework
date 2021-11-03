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
    $db->query('CREATE TABLE tb_users (id BIGINT AUTO_INCREMENT NOT NULL, email NVARCHAR(320) NOT NULL, passwd VARCHAR(255) NOT NULL, displayname NVARCHAR(200) NOT NULL, CONSTRAINT PK PRIMARY KEY (id));');
    $db->query('CREATE TABLE tb_usergroups (id BIGINT AUTO_INCREMENT NOT NULL, displayname NVARCHAR(255) NOT NULL, CONSTRAINT PK PRIMARY KEY (id));');
    $db->query('CREATE TABLE tb_users_usergroups (user BIGINT NOT NULL, usergroup BIGINT NOT NULL, CONSTRAINT PK PRIMARY KEY (user,usergroup), CONSTRAINT FK_user FOREIGN KEY (user) REFERENCES tb_users(id), CONSTRAINT FK_usergroup FOREIGN KEY (usergroup) REFERENCES tb_usergroups(id));');
    $db->query('CREATE TABLE tb_users_permissions (id BIGINT AUTO_INCREMENT NOT NULL, user BIGINT NOT NULL, permission NVARCHAR(255), CONSTRAINT PK PRIMARY KEY (id), CONSTRAINT FK_userperms_user FOREIGN KEY (user) REFERENCES tb_users(id));');
    $db->query('CREATE TABLE tb_usergroups_permissions (id BIGINT AUTO_INCREMENT NOT NULL, usergroup BIGINT NOT NULL, permission NVARCHAR(255), CONSTRAINT PK PRIMARY KEY (id), CONSTRAINT FK_groupperms_usergroup FOREIGN KEY (usergroup) REFERENCES tb_usergroups(id));');

    $db->query('CREATE FULLTEXT INDEX users_names ON tb_users(displayname, email);');
    
    //TODO: Interactive setup to create administrative account
    $db->query('INSERT INTO tb_users (email, passwd, displayname) VALUES ("administrator","$argon2id$v=19$m=1024,t=2,p=2$UUxDd2xleVU2cUlqdEVwVQ$nCqeHS2fTuNPn9uoYliSbl8Epp/R8bEGoTP6w6qZdSo","Administrator");');
    $db->query('INSERT INTO tb_users (email, passwd, displayname) VALUES ("GUEST","","Guest");');
    $db->query('INSERT INTO tb_core VALUES ("version","1.0");');
}