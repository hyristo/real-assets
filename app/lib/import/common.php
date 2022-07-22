<?php
require_once ROOT . "lib/classes/Utils/Utils.php";
require_once ROOT . "lib/classes/Utils/EmailSms.php";
require_once ROOT . "lib/classes/Utils/Date.php";
if ($config['FILE_UPLOAD'] == true) {
    require_once ROOT . "lib/classes/Utils/File.php";
    require_once ROOT . "lib/classes/ModuloPdf.php";
}
require_once ROOT . "lib/classes/Utils/CodiceFiscale.php";

require_once ROOT . "lib/classes/Comune.php";
require_once ROOT . "lib/classes/Logs.php";
?>

