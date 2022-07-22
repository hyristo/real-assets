<?php
ob_start();
session_name('AppManager');
session_start([
    'read_and_close' => true,
]);

require_once "app.config.php";
require_once ROOT . "lib/constants.php";
require_once ROOT . "lib/import/common.php";
//require_once ROOT . "lib/classes/Utils/Date.php";
//require_once ROOT . "lib/classes/Utils/Utils.php";
//$statoSportello = Utils::GetStatoSportello();
$statoSportello = Utils::checkAuthStatoSportelloHome();
global $statoSportello;