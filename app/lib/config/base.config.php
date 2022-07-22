<?php
/* CONFIGURAZIONI SISTEMA */

define("APP_NAME", "RealAssets"); //Beni reali in inglese
define("APP_DESCR", "Gestione del patrimonio differenziato in: immobili, terreni, strade e verde urbano");
define("BASE_HTTP", PREFIX_HTTP . $_SERVER['HTTP_HOST'] . BASE_FOLDER);
define("PRIVATE_SECTION", "modules/private/"); // folder per file area post-login
define("HTTP_PRIVATE_SECTION", BASE_HTTP . PRIVATE_SECTION); // folder per file area post-login
define("PATH_LOGIN", BASE_HTTP . "login.php");
define("WS_CALL", BASE_FOLDER . "webservice/_webservice.php"); // CHIAMATA BASE WEBSERVICE

define("COOKIE_LIFETIME", 3600); // MAX DI INATTIVITA DELL'UTENTE IN SECONDI
//define("GC_MAXLIFETIME", 60); // SECONDI DI INCREMENTO DEL COOKIE_LIFETIME AD OGNI CHIAMATA DI ATTIVITA
define("MIN_PASSWORD_LENGHT", 8); // Numero min caratteri pwd
define("TIME_SCADENZA_PASSWORD", 180); // numero di giorni per la scadenza della password
define("ENABLE_CHECK_IP", false); // controllo sugli ip => true => ATTIVO ; false => NON ATTIVO
define("UNICO_JS_CSS", false); // Imostato a true carica un unico file js e css se presente
define("TEST_CSS", false);

/* Moduli da escludere nei logs */
$GLOBALS["MODULI_NO_LOGS"] = array("Notifica", "AppToken");

/*
 *  CONTROLLO MODULI E SERVIZI
 *  moduli e servizi richiamabili senza essere loggati array('module' => array('action')) [il controllo è fatto nella pagina "_webservice.php"]
 */
//$pageAccessNoControll = array('info.php', 'faq.php', 'login.pho', 'loginspid.php', 'login_spid.php', '_webservice.php','privacy.php');
//$pageAccessNoControll = array('login.php', 'loginspid.php', 'login_spid.php', '_webservice.php','privacy.php', 'listmemorycard.php');
$pageAccessNoControll = array('login.php', '_webservice.php');
$moduliActionAccessNoControll = array(
    'account' => array('login', 'save', 'searchComune')
);
?>