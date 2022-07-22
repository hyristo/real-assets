<?
if(ATTIVA_NAVBAR){
?>
<div class="col-md-3 col-xl-2 bd-sidebar">          
<?
    require_once ROOT . "lib/classes/Menu.php";    
    $mainMenu = new Menu(true);
    echo $mainMenu->RenderNavBar("sidebarMenu", "slide-out");    
    $colCssContainer = "col-md-9 ml-sm-auto col-lg-10 px-md-4";
    ?>
</div>
<?
}
?>