<!doctype html>
<?
include '../../lib/api.php';

?>
<html lang="en">
    <? include_once ROOT . 'layout/head.php'; ?>
    <body>
        <?
        include_once ROOT . 'layout/header.php';
        ?>
        <main role="main">
            <header class="masthead masthead-page">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-3 col-sm-3">
                            <i style="font-size: 155px" class="fas fa-tractor"></i>
                        </div>
                        <div class="col-lg-5 col-sm-5"></div>
                        <div class="col-lg-4 col-sm-4 text-right">
                            <h2>QUADERNO DI CAMPAGNA</h2>
                            <h4>Il software online per l'agricoltura sostenibile</h4>
                            <small>ti aiuta a gestire la tua azienda agricola, <br/>rendendola efficiente e conforme alla normativa vigente</small>
                        </div>
                        
                    </div>
                </div>
                <?
                include_once ROOT . 'layout/header_svg.php';
                ?>
            </header>
            <div class="jumbotron">
                <h1 class="display-4">Accesso, non consentito!</h1>
                <p class="lead">Non hai i permessi per accedere al modulo.</p>
                <hr class="my-4">                
              </div>
        </main>
        <? include_once ROOT . 'layout/footer.php'; ?>       
    </body>
</html>
