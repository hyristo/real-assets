<?php
ob_start();
//ini_set('session.gc_maxlifetime', 3600);
//session_set_cookie_params(3600);
define("SESSION_NAME", 'AppManager');
session_name(SESSION_NAME);
session_start([
    'read_and_close' => true,
]);

require_once "app.config.php";
require_once ROOT . "lib/constants.php";
require_once ROOT . "lib/classes/Menu.php";

/* CLASSI DI UTILITA' */
require_once ROOT . "lib/import/common.php";

/* APPLICATIVO */
require_once ROOT . "lib/classes/Database.php";
require_once ROOT . "lib/classes/Application.php";
require_once ROOT . "lib/classes/Account.php";
require_once ROOT . "lib/classes/AccountGruppi.php";
require_once ROOT . "lib/classes/UidFirebaseAdmin.php";
require_once ROOT . "lib/classes/Module.php";
require_once ROOT . "lib/classes/Moduli.php";
require_once ROOT . "lib/classes/AccountAnagrafica.php";
require_once ROOT . "lib/classes/AccessLog.php";

if ($config['Faq']) {
    require_once ROOT . "lib/classes/Faq.php";
}
if ($config['SPID']) {
    require_once ROOT . "lib/classes/SPID.php";    
}

if ($config['GESTIONALE']) {
    require_once ROOT . "lib/import/gestionale.php";    
}
if ($config['PATRIMONIO']) {
    require_once ROOT . "lib/import/patrimonio.php";
}


/* START APP */
if (TEST_INTRUSIONE) {
    $account = SPID::loginFakeStressTest(); /// TEST INTRUSIONE
}
$statoSportello = Utils::checkAuthStatoSportello();

$app = new Application();

switch ($app->lastEsito) {
    case Application::SUCCESS:
        $LoggedAccount = $app->getAccount();            

        
        $con = $app->getCon();
        $conSpid = $app->getConSpid();
        break;
    case Application::ACCESS_ERR:
        exit(json_encode(Utils::initDefaultResponseDataTable(-999, $app->lastMessageError)));
//            exit(json_encode(Utils::initDefaultResponse(-999, $app->lastMessageError)));
        break;
    case Application::ACCESS_WS_ERR:
        exit(0);
        break;
}

global $LoggedAccount, $con, $conSpid, $MENUITEMS, $NAVITEMS, $statoSportello, $GRUPPI_CODICIVARI, $CODICI_ERRORE_UPLOAD_P7M, $codiciErroreNoControll,$DESCRIZIONEMOVIMENTO, $MESI, $ESTENSIONI_FOTO, $ESTENSIONI_ALLEGATI;

