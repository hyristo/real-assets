<!doctype html>
<?include '../../lib/api.php';
define ('WS_MODULE', 'logs');
$mode = (!isset($_GET['mode']) ? 'list' : $_GET['mode']);
?>
<html lang="en">
	<?include_once ROOT . 'layout/head.php';?>
	<body>
	<?include_once ROOT . 'layout/header.php';?>

      <?include_once ROOT . 'layout/menu.php';?>
      
      
	<main role="main" class="<?=$cssColumNavBar?>">
  <div class="container-fluid">
            <?php 
            switch ($mode) {
                case 'list': // LISTA LOGS?>
                    <div class="row">
                        <div class="col-sm-12">

                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Logs</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                      <div class="card">
                          <div class="card-header">
                              <h4 class="card-title"> Elenco logs</h4>
                          </div>
                          <div class="card-body">
                        <div class="row">
                                <div class="col s12">
                                        <table id="lista_Logs" class="table table-striped table-bordered" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>IP</th>
                                                    <th>Username</th>
                                                    <th>data</th>
                                                    <th>Modulo</th>
                                                    <th>action</th>
                                                    <th>visualizza</th>
                                                </tr>
                                            </thead>
                                        </table>
                               </div>
                           </div>
                          </div>
                      </div>
            <?php
                break; // FINE LISTA LOGS
                case 'viewLog':?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                          <li class="breadcrumb-item"><a href="<?=BASE_HTTP?>modules/logs/logs.php">Logs</a></li>
                          <li class="breadcrumb-item active" aria-current="page">Anteprima log</li>
                        </ol>
                    </nav>
                    <div class="row">
                    <?php
                    $id_log = intval($_REQUEST['id']);
                    $record = new Logs($id_log);

                    $campi = $record->ParseMessage($arr);                    
                    ?>
                    
                    
                    <div id="ecommerce-products" class="col s12">
                        <div class="card">
                          
                          <div class="card-body">
                              <h2 class="card-title">LOG</h2>
                              <h3>MODULO: <span class="badge badge-secondary"><?=$record->MODULE?></span></h3>
                              <h4>DATA OPERAZIONE: <span class="badge badge-secondary"><?= Date::FormatDate($record->DATA)?></span></h4>
                              <h5>ACTION: <span class="badge badge-secondary"><?=$record->ACTION?></span></h5>
                              <h6>IP: <span class="badge badge-secondary"><?=$record->IP?></span></h6>
                                    
                                <hr class="mb-1">
                            
                            <div class="row">
                                  <div class="col s12 m4 l4">
                                    <div class="alert alert-dark" >
                                    <p>
                                      <strong>CAMPO</strong>
                                    </p>
                                    <p>Nome del campo </p>
                                    </div>
                                  </div>
                                    <div class="col s12 m4 l4">
                                      <div class="alert alert-dark" >
                                      <p>
                                        <strong>PRIMA</strong>
                                      </p>
                                      <p>Valore prima delle modifiche</p>
                                      </div>
                                    </div>
                                    <div class="col s12 m4 l4">
                                        <div class="alert alert-dark" >
                                      <p>
                                        <strong>DOPO</strong>
                                      </p>
                                      <p>Valore successivo alle modifiche.</p>
                                      </div>
                                    </div>
                            </div>
                                <?php foreach ($campi as $c){?>
                                <div class="row">
                                  <div class="col s12 m4 l4">
                                    <div class="alert <?=($c['Variato'] ? 'alert-warning': 'alert-primary')?> ">                                      
                                        <p><?=($c['Variato'] ? '<i class="fas fa-flag"></i>': '')?> <?=($c['Campo'] !="" ? $c['Campo'] : '-' )?></p>
                                    </div>
                                  </div>
                                  <!--DARK-->
                                  <div class="col s12 m4 l4">
                                    <div class="alert <?=($c['Variato'] ? 'alert-warning': 'alert-primary')?> ">         
                                        <p><?=($c['OldValue'] !="" ? $c['OldValue'] : '-' )?></p>
                                    </div>
                                  </div>
                                  <!--WITH ICON-->
                                  <div class="col s12 m4 l4">
                                    <div class="alert <?=($c['Variato'] ? 'alert-warning': 'alert-primary')?> ">         
                                        <p><?=($c['Valore'] !="" ? $c['Valore'] : '-' )?></p>                                      
                                    </div>
                                  </div>
                                  </div>  
                                <?php }?>
                              
                            
                          </div>
                        </div>
                      </div>
                    

                </div>
                <?php
                break;   
                }?>
                  </div>
	</main>
	<?include_once ROOT . 'layout/footer.php';?>

    <script type="text/javascript">
            $(document).ready(function() {
                 $('.modal').modal();
                 
                 $('#lista_Logs').DataTable({
                    
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    "order": [[ 0, 'desc' ]],
                    'ajax': {
                        'url': WS_CALL+'?module=<?=WS_MODULE?>&action=listDataTable'
                    },
                    'columns': [
                       { data: 'ID' },
                       { data: 'IP' },
                       { data: 'USERNAME' },
                       { data: 'DATA' },
                       { data: 'MODULE' },
                       { data: 'ACTION' },
                       { data: 'visualizza' }
                    ]
                 });

            });
         
         
         
         
        </script>
    
	</body>
</html>
