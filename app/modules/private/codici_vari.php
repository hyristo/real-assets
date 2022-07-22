<!DOCTYPE html>
<?php
include '../../lib/api.php';
define('WS_MODULE', 'codici_vari');
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
                                <li class="breadcrumb-item active" aria-current="page">Gestione codici vari</li>
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
                                    <input type="hidden" id="edit_ID_CODICE" readonly="readonly" name="ID_CODICE" />                            

                                    <div class="row">
                                        <label class="col-sm-3"><span class="text-danger">*&nbsp;</span>NOME</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="text" id="edit_DESCRIZIONE" required="" name="DESCRIZIONE" required>
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        <label class="col-sm-3"><span class="text-danger">*&nbsp;</span>GRUPPO</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="text" id="edit_GRUPPO" required="" name="GRUPPO" required>
                                        </div>
                                    </div><br>
                                    <div class="row">
                                        <label class="col-sm-3"><span class="text-danger">*&nbsp;</span>SIGLA</label>
                                        <div class="col-sm-9">
                                            <input class="form-control" type="text" id="edit_SIGLA" required="" name="SIGLA" required>
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
                                    <input type="hidden" id="ID_CODICE" name="ID_CODICE" />                            
                                    <div class="row form-group">
                                        <label class="col-sm-3"><span class="text-danger">*&nbsp;</span>GRUPPO</label>
                                        <div class="col-sm-9">
                                            <select class="form-control" id="GRUPPO" placeholder="Gruppo" required="" name="GRUPPO" >
                                                <option value="" >Seleziona il gruppo</option>
                                                <? foreach ($GRUPPI_CODICIVARI as $key => $value) { ?>
                                                    <option value="<?= $key ?>"><?= $value ?></option>
                                                <? } ?>
                                            </select>  
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <label class="col-sm-3"><span class="text-danger">*&nbsp;</span>NOME</label>
                                        <div class="col-sm-9">
                                            <input autocomplete="off" class="form-control" type="text" id="DESCRIZIONE" required="" name="DESCRIZIONE" required>
                                        </div>
                                    </div>
                                    
                                    <div class="row form-group">
                                        <label class="col-sm-3"><span class="text-danger">*&nbsp;</span>SIGLA</label>
                                        <div class="col-sm-9">
                                            <input autocomplete="off" class="form-control" type="text" id="SIGLA" required="" name="SIGLA" required>
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
                <div class="card">
                    <div class="card-header">
                        <nav class="navbar navbar-expand-lg navbar-light bg-light">
                            <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
                                <li class="nav-item active">
                                    <span class="navbar-brand mb-0 h1">Gestione codici vari</span>
                                </li>
                            </ul>
                            <span class="navbar-text">
                                        <button class="btn btn-sm btn-success" type="button" onclick="" data-toggle="modal" data-target="#newModalCodice" title="Nuovo movimento di carico" ><i class="fas fa-laptop-code"></i> Nuovo codice</button>
                            </span>
                        </nav>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">

                                <table id="ListCodiciVari" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Gruppo</th>
                                        <th>Descrizione</th>
                                        <th>Sigla</th>
                                        <th>Modifica</th>
                                        <th>Cancella</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th></th>
                                        <th>Gruppo</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    </tfoot>
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


                $('#ListCodiciVari').DataTable({
                    initComplete: function () {
                        this.api().columns([1]).every(function () {
                            var column = this;
                            var select = $('<select class="selectForPages"><option value=""></option></select>')
                                    .appendTo($(column.header()))
                                    .on('change', function () {
                                        var val = $.fn.dataTable.util.escapeRegex(
                                                $(this).val()
                                                );

                                        column
                                                .search(val ? val : '', true, false)
                                                .draw();
                                    });

                            column.cells('', column[0]).render('display').sort().unique().each(function (d, j) {
                                if (column.search() === d) {
                                    select.append('<option value="' + d + '" selected="selected">' + d + '</option>');
                                } else {
                                    select.append('<option value="' + d + '">' + d + '</option>');
                                }
                            });

                            $(select).click(function (e) {
                                e.stopPropagation();
                            });

                        });
                    },
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    'ajax': {
                        'url': WS_CALL + '?module=<?= WS_MODULE ?>&action=list'
                    },
                    'columns': [
                        {data: 'ID_CODICE'},
                        {data: 'GRUPPO'},
                        {data: 'DESCRIZIONE'},
                        {data: 'SIGLA'},
                        {data: 'modifica'},
                        {data: 'cancellato'}/*,
                         { data: 'invisibile' }*/
                    ]
                });


            });


        </script>
    </body>
</html>