<?php

define("DEV", 1);
define("DEBUG_MODE", 0);
define("TEST_INTRUSIONE", false);// PERMETTE DI FARE I TEST SENZA PASSARE DAL LOGIN SETTA UNA SESSIONE FAKE 

$config = array(
    "DEBUG_MESSAGE" => false,
    "FAQ" => true,
    "SPID" => true,
    "FILE_UPLOAD" => true,
    "FIREBASE" => false,
    "CAPTCHA" => false,
    "EMAIL" => false    
);

if ($config['DEBUG_MESSAGE'] == true) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

define("PREFIX_HTTP", "http://");
define("BASE_FOLDER", "/"); // deve chiudere sempre con /
$GLOBALS['HTTP'] = PREFIX_HTTP . $_SERVER['HTTP_HOST'] . BASE_FOLDER . '/';
$GLOBALS['ROOT'] = PREFIX_HTTP . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']) . '/';
define("ROOT", dirname(dirname(__FILE__)) . "/");

/* MODULI OBBLIGATORI */
require_once 'config/base.config.php'; // BASE
require_once 'config/database.config.php'; // DATABASE
require_once 'config/menu.config.php'; // VOCI DI MENU'
require_once 'config/sportello.config.php'; // CFG SPORTELLO (DATE E CONSTANT)

/* SPID */
if ($config['SPID'] == true) {
    require_once 'config/spid.config.php';
}

/* FILE UPLOAD */
if ($config['FILE_UPLOAD'] == true) {
    require_once 'config/upload.config.php';
}

/* FIREBASE */
if ($config['FIREBASE'] == true) {
    require_once 'config/firebase.config.php';
}

/* CAPTCHA */
if ($config['CAPTCHA'] == true) {
    require_once 'config/captcha.config.php';
}