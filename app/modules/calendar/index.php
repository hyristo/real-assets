<?php
include '../../lib/api.php';
define('WS_MODULE', 'calendar');
define("THIS_PERMISSION", array('CODICI_VARI'));
include_once ROOT.'/layout/include_permission.php';

?>
<html lang="en">
<? include_once ROOT . 'layout/head.php'; ?>

<body>
<? include_once ROOT . 'layout/header.php'; ?>
    <!--link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css"-->

<link rel="stylesheet" href="css/calendar.css">
<main role="main" class="<?= $cssColumNavBar ?>" >
    <div class="container-fluid">

        <div class="row">
            <div class="col-sm-12">

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Scadenziario</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="container-fluid">
            <div class="card">
                <div class="card-header page-header">
                    <div class="pull-right form-inline">
                        <div class="btn-group">
                            <button class="btn btn-primary" data-calendar-nav="prev"><i class="fas fa-angle-double-left"></i>&nbsp; Prec.</button>
                            <button class="btn btn-default" data-calendar-nav="today">Oggi</button>
                            <button class="btn btn-primary" data-calendar-nav="next">Succ.&nbsp;<i class="fas fa-angle-double-right"></i></button>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-warning" data-calendar-view="year">Anno</button>
                            <button class="btn btn-warning active" data-calendar-view="month">Mese</button>
                            <button class="btn btn-warning" data-calendar-view="week">Settimana</button>
                            <button class="btn btn-warning" data-calendar-view="day">Giorno</button>
                        </div>
                    </div>
                    <h3></h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="showEventCalendar"></div>
                        </div>
                        <!--div class="col-md-3">
                                <h4>Prossimi eventi</h4>
                                <ul id="eventlist" class="nav nav-list"></ul>
                        </div-->
                    </div>
                </div>
            </div>
        </div>


    </div>

</main>

    <!-- BEGIN: Footer-->
<?php include_once ROOT . 'layout/footer.php'; ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.8.3/underscore-min.js"></script>
<script type="text/javascript" src="js/calendar.js"></script>
<script type="text/javascript" src="js/language/it-IT.js"></script>
<script type="text/javascript" src="js/events.js"></script>
<script type="text/javascript">
    function goToContratto(id_patrimonio, id_contratto) {
        var obj = {
            id: id_contratto,
            id_patrimonio: id_patrimonio
        };
        $.redirect(HTTP_PRIVATE_SECTION + "inc_patrimonio/page_contratto.php", obj);
    }
</script>
