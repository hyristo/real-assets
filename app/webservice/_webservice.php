<?

include '../lib/api.php';

$accessModuleControll = true; // attivo il controllo sul modulo
$accessActionControll = true; // attivo il controllo sul'action
//Utils::print_array($moduliActionAccessNoControll);
foreach ($moduliActionAccessNoControll as $mod => $a) {
    if ($mod == $_REQUEST["module"]) {
        $accessModuleControll = false;
    }

//    echo $mod ."==". $_REQUEST["module"]."<br>";
    if (!$accessModuleControll) {
        if (count($a) > 0) {
            foreach ($a as $act) {
                if ($act == $_REQUEST["action"]) {
                    $accessActionControll = false;
                }
//                echo $act ."=========". $_REQUEST["action"]."<br>";
            }
        } else {

            $accessActionControll = false;
        }
    }
}
if ($accessModuleControll || $accessActionControll) {
//    echo "eseguo il controllo";
    if (!Utils::canAccess()) {
        echo "Accesso non autorizzato";
        exit(0);
    }
}

$module = stripslashes($_REQUEST["module"]);
if (file_exists("$module.php"))
    include $module . ".php";
