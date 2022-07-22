<!doctype html>
<?
include '../../lib/api.php';
define('WS_MODULE', 'account');
$this_permission = array(GRUPPO_OPERATORI);
include_once (ROOT . "layout/include_permission.php");
?>
<html lang="en">
    <? include_once ROOT . 'layout/head.php'; ?>
    <body>
        <? include_once ROOT . 'layout/header.php'; ?>
        
            <? include_once ROOT . 'layout/menu.php'; ?>
            <main role="main" class="<?= $cssColumNavBar ?>" >
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Operatori</li>
                            </ol>
                        </nav>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <h4 class="card-title">Lista Operatori</h4>
                        <table id="listaOperatori" class="table table-striped table-bordered" >
                            <thead>
                                <tr>
                                    <!--<th>ID</th>-->
                                    <th>Nome</th>
                                    <th>Cognome</th>
                                    <th>Mail</th>
                                    <th>Modifica</th>
                                    <!--<th>Elimina</th>-->
                                </tr>
                            </thead>                               
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <button type="button" class="btn btn-primary btn-sm btn-block" data-toggle="modal" data-target="#exampleModal">
                            Nuovo Operatore <i class="fa fa-users"></i>
                        </button>
                    </div>
                    <div class="col-sm-6">
                        <a href="https://console.firebase.google.com/project/sipars-backend-users/overview?pli=1" target="_blank" class="btn btn-success btn-sm btn-block" > Google Dashboard <i class="fab fa-google"></i></a>
                    </div>
                </div>
                <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document" >
                        <div class="modal-content" style="width:110%">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Operatore</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="javascript:createOperatore()" id="form-register" >
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group row">
                                                <label for="inputPassword3" class="col-sm-3 col-form-label"><span class="text-danger">*&nbsp;</span>Nome</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control"  required="" name="NOME" id="nome">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputPassword3" class="col-sm-3 col-form-label"><span class="text-danger">*&nbsp;</span>Cognome</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control"  required="" name="COGNOME" id="cognome">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 col-form-label"><span class="text-danger">*&nbsp;</span>Email</label>
                                                <div class="col-sm-9">
                                                    <input type="email" class="form-control" required="" name="EMAIL" id="email">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputPassword3" class="col-sm-3 col-form-label"><span class="operatore_text-danger">*&nbsp;</span>Password<br></label>


                                                <div class="col-sm-9">
                                                    <input type="password" class="form-control"  required="" name="PASSWORD" id="password" >
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <span class="small text-danger"><i class="fa fa-info-circle"></i>(Lunghezza Minima 8 Caratteri)</span> 
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                    <button type="submit"  class="btn btn-primary" >Prosegui &nbsp;<i class="fas fa-arrow-circle-right"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="edit-operatore" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document" >
                        <div class="modal-content" style="width:110%">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Modifica Operatore</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="javascript:editOperatore()" id="form-edit-operatore" >
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div class="form-group row">
                                                <label for="inputPassword3" class="col-sm-3 col-form-label"><span class="text-danger">*&nbsp;</span>Nome</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control"  required="" name="NOME" id="operatore_NOME">
                                                    <input type="hidden" class="form-control"  required="" name="ID" id="operatore_ID">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputPassword3" class="col-sm-3 col-form-label"><span class="text-danger">*&nbsp;</span>Cognome</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control"  required="" name="COGNOME" id="operatore_COGNOME">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label for="inputEmail3" class="col-sm-3 col-form-label"><span class="text-danger">*&nbsp;</span>Email</label>
                                                <div class="col-sm-9">
                                                    <input type="email" class="form-control" required="" name="EMAIL" id="operatore_EMAIL">
                                                </div>
                                            </div>
                                            <br>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                    <button type="submit"  class="btn btn-primary" >Prosegui &nbsp;<i class="fas fa-arrow-circle-right"></i></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            </main>
            <? include_once ROOT . 'layout/footer.php'; ?>
        
    </body>
    <script src="<?= BASE_HTTP ?>js/operatori.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#listaOperatori').DataTable({
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                "order": [[0, 'desc']],
                'ajax': {
                    'url': WS_CALL + '?module=<?= WS_MODULE ?>&action=listaO'
                },
                'columns': [
                    {data: 'NOME'},
                    {data: 'COGNOME'},
                    {data: 'EMAIL'},
                    {data: 'MODIFICA'},
//                    {data: 'ELIMINA'}

                ]

            });

        });


    </script>    

</html>