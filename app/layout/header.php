<?
//Utils::checkLogin();
//Utils::checkViewConsent();
require_once ROOT . "lib/classes/Menu.php";
$mainMenu = new Menu(true);
$cssColumNavBar = "container-fluid";
$colCssContainer = "col-md-12";
if (ATTIVA_MENUBAR) {
    //echo $mainMenu->RenderMenuBar("navbarCollapse", "MenuOr");
    echo $mainMenu->RenderStaticNavBar("navbarCollapse", "MenuOr");
} else {
    $cssColumNavBar = "col-md-9 ml-sm-auto col-lg-10 px-md-4";
    ?>
    
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-primary">
        <!--img src="<?=BASE_HTTP?>assets/img/sicilia.png" alt="" class="logo"/-->
        <a class="navbar-brand" href="#"><?= APP_NAME ?></a>    
    </nav>  
<? }?>
<? include_once ROOT . 'layout/loader.php'; ?>
