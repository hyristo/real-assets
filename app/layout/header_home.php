<?
include_once 'lib/api_s.php';
?>
<nav class="navbar navbar-expand-lg navbar-dark fixed-top bg-primary">
    <!--img src="<?= BASE_HTTP ?>assets/img/sicilia.png" alt="" class="logo"/-->
    <a class="navbar-brand" href="index.php"><i class="fab fa-battle-net"></i>&nbsp;<?= APP_NAME ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#MenuOr" aria-controls="MenuOr" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarCollapse">
        <ul class="navbar-nav lg-auto mb-2 mb-lg-0"> 
            <? // Menu::renderHomeStaticMenu(); ?>
        </ul>
    </div>
</nav>  