<?php
require_once "app.config.php";
require_once ROOT . "lib/constants.php";
require_once ROOT . "lib/classes/Menu.php";
require_once ROOT . "lib/classes/Utils/Date.php";
require_once ROOT . "lib/classes/Utils/Utils.php";
$statoSportello = Utils::GetStatoSportello();
global $statoSportello;