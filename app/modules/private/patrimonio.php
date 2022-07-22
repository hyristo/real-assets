<!DOCTYPE html>
<?php
include '../../lib/api.php';
define('WS_MODULE', 'patrimonio');
define("THIS_PERMISSION", array('CODICI_VARI'));
include_once ROOT.'/layout/include_permission.php';

?>
<html lang="en">
    <? include_once ROOT . 'layout/head.php'; ?>

    <body>
        <? include_once ROOT . 'layout/header.php'; ?>
        <main role="main" class="<?= $cssColumNavBar ?>" >
            <div class="container-fluid">
                
                <div class="row">
                    <div class="col-sm-12">

                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Gestione del patrimonio</li>
                            </ol>
                        </nav>
                    </div>
                </div>                                    
                <div class="card">
                    <div class="card-header">
                        <nav class="navbar navbar-expand-lg navbar-light bg-light">
                            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                                <li class="nav-item active">
                                    <span class="navbar-brand mb-0 h1">Gestione dei beni patrimoniali</span>
                                </li>
                            </ul>
                            <span class="navbar-text">
                                <button class="btn btn-sm btn-success" type="button" onclick="goToPage('page_patrimonio',0)" title="Nuovo bene patrimoniale" ><i class="fas fa-laptop-code"></i> Nuovo bene</button>
                            </span>
                        </nav>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">

                                <table id="ListPatrimonio" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Indirizzo</th>
                                        <th>Comune</th>
                                        <th>Provincia</th>
                                        <th>Particella</th>
                                        <th>Modifica</th>
                                        <th>Cancellato</th>
                                    </tr>
                                    </thead>

                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </main>
<!-- BEGIN: Footer-->
<?php include_once ROOT . 'layout/footer.php'; ?>
            <!-- END: Footer-->
        <script type="text/javascript">
            $(document).ready(function () {


                var tableListPatrimonio = $('#ListPatrimonio').DataTable({
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    //select: true,
                    'ajax': {
                        'url': WS_CALL + '?module=<?= WS_MODULE ?>&action=list'
                    },
                    'columns': [
                        {data: 'ID'},
                        {data: 'NOME'},
                        {data: 'INDIRIZZO'},
                        {data: 'COMUNE'},
                        {data: 'PROVINCIA'},
                        {data: 'PARTICELLA'},
                        {data: 'modifica'},
                        {data: 'cancellato'}
                    ]
                });

            });


        </script>
    </body>
</html>