<!doctype html>
<?
include 'lib/api_s.php';

?>
<html lang="en">
    <? include_once ROOT . 'layout/head.php'; ?>
    <body >
        <?
        include_once ROOT . 'layout/header_home.php';
        //include_once ROOT . 'layout/menu.php'; 
        ?>
        <main role="main" >            
            <header class="masthead masthead-page">
                <div class="container">
                    <div class="row">
                        <div class="col-9 text-right">
                            <h2>QUADERNO DI CAMPAGNA</h2>
                            <h3>Il software online per l'agricoltura sostenibile</h3>
                            <small>ti aiuta a gestire la tua azienda agricola, <br/>rendendola efficiente e conforme alla normativa vigente</small>
                        </div>
                        <div class="col-3">
                            <i style="font-size: 155px" class="fas fa-tractor"></i>
                        </div>                    
                    </div>
                </div>
                <?
                include_once ROOT . 'layout/header_svg.php';
                ?>
            </header>            
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <img src="assets/img/notfound.png" alt="404" class="img-fluid"/>
                    </div>
                    <div class="col-md-2"></div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-center" >
                        <center><h1>OPS!!! Qualcosa è andato storto</h1></center>
                    </div>
                </div>
            </div>
        </main>		
        <? include_once ROOT . 'layout/footer.php'; ?>
    </body>

</html>
