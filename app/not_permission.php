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
                    <div class="col s12">
                        <div class="card">
                            <div class="card-content" style="overflow: auto;">                                        
                                <div id="maintenance" class="col s12 center-align white">                                    
                                    <h4 class="error-code">ACCESSO NEGATO</h4>
                                    <h6 class="mb-2 mt-2">Non disponi dei permessi per accedere al modulo</h6>
                                </div>
                            </div>
                        </div>
                    </div>                                        
                </div>                
            </div>
        </main>		
        <? include_once ROOT . 'layout/footer.php'; ?>
    </body>

</html>
