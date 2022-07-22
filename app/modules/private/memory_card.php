<!DOCTYPE html>
<?php
include '../../lib/api.php';
define('WS_MODULE', 'memory_card');
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
                                <li class="breadcrumb-item active" aria-current="page">Gestione Mamory card</li>
                            </ol>
                        </nav>
                    </div>
                </div>                                    
                <div class="modal fade" id="editCodice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="max-width: 70%;"  >
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4>Modifica Codice</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form class="col s12" id="form-edit-codice" action="javascript:realAssetsFramework.ModModal('form-edit-codice', '<?= WS_MODULE ?>')">
                                <!--Set dropzone height-->
                                <div class="modal-body">


                                    <input type="hidden" id="action"  name="action" value="save" />
                                    <input type="hidden" id="edit_ID" readonly="readonly" name="ID" />                            

                                    <div class="row">
                                        <label class="col-sm-3"><span class="text-danger">*&nbsp;</span>CODICE</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="text" id="edit_CODICE" required="" name="CODICE" required>
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        <label class="col-sm-3"><span class="text-danger">*&nbsp;</span>NOTE</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="text" id="edit_NOTE" required="" name="NOTE" required>
                                        </div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                    <button type="submit" class="btn btn-primary" >Salva</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="modal fade" id="newModalCodice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">    
                    
                    <div class="modal-dialog" role="document" style="max-width: 70%;"  >
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4>Nuovo Codice</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form class="col s12" id="form-add-codice" action="javascript:realAssetsFramework.ModModal('form-add-codice', '<?= WS_MODULE ?>')">
                                <!--Set dropzone height-->
                                <div class="modal-body">


                                    <input type="hidden" id="action"  name="action" value="save" />
                                    <input type="hidden" id="ID" name="ID" />                                                                
                                    <div class="row form-group">
                                        <label class="col-sm-3"><span class="text-danger">*&nbsp;</span>CODICE</label>
                                        <div class="col-sm-9">
                                            <input autocomplete="off" class="form-control" type="text" id="CODICE" required="" name="CODICE" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row form-group">
                                        <label class="col-sm-3"><span class="text-danger">*&nbsp;</span>NOTE</label>
                                        <div class="col-sm-9">
                                            <input autocomplete="off" class="form-control" type="text" id="NOTE" required="" name="NOTE" required>
                                        </div>
                                    </div>                                


                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                                    <button type="submit" class="btn btn-primary" >Salva</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <nav class="navbar navbar-expand-lg navbar-light bg-light">                                
                            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                                <li class="nav-item active">
                                    <span class="navbar-brand mb-0 h1">Gestione Mamory card</span>
                                </li>
                            </ul>
                            <span class="navbar-text">
                                <button class="btn btn-sm btn-success" type="button" onclick="" data-toggle="modal" data-target="#newModalCodice" title="Nuovo movimento di carico" ><i class="fas fa-laptop-code"></i> Nuovo codice</button>                            
                            </span>
                        </nav> 
                        <table id="ListCard" class="table table-striped table-bordered" style="width:100%">                                    
                            <thead>
                                <tr>
                                    <th>ID</th>                                    
                                    <th>CARD</th>
                                    <th>Note</th>
                                    <th>Modifica</th>
                                    <th>Attiva</th>
                                </tr>
                            </thead>                            
                        </table>                              
                        
                    </div>
                </div>


            </div>
            
        </main>
<!-- BEGIN: Footer-->
<?php include_once ROOT . 'layout/footer.php'; ?>
            <!-- END: Footer-->
        <script type="text/javascript">
            $(document).ready(function () {


                $('#ListCard').DataTable({                    
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    'ajax': {
                        'url': WS_CALL + '?module=<?= WS_MODULE ?>&action=list'
                    },
                    'columns': [
                        {data: 'ID'},
                        {data: 'CODICE'},
                        {data: 'NOTE'},                        
                        {data: 'modifica'},
                        {data: 'cancellato'}/*,
                         { data: 'invisibile' }*/
                    ]
                });


            });


        </script>
    </body>
</html>