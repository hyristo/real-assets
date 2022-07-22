<?
require_once ROOT . "lib/classes/Menu.php";
$mainMenu = new Menu(false);
//echo "CI ENTRO";
$cssColumNavBar = "container-fluid";
if(ATTIVA_NAVBAR){
    echo $mainMenu->RenderNavBar("sidebarMenu", "slide-out");
    $cssColumNavBar = "col-md-9 ml-sm-auto col-lg-10 px-md-4";
}
?>

