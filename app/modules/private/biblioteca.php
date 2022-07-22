<!DOCTYPE html>
<?php
include '../../lib/api.php';
define('WS_MODULE', 'biblioteca');
define("THIS_PERMISSION", array('BIBLIOTECA'));
include_once ROOT.'/layout/include_permission.php';

$tipo_contenuto = CodiciVari::Load(0, 'TIPO_CONTENUTO');
$categoria = CodiciVari::Load(0, 'CATEGORIA_LIBRO');
$genere = CodiciVari::Load(0, 'GENERE_LIBRO');


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
                                <li class="breadcrumb-item active" aria-current="page">Gestione della biblioteca</li>
                            </ol>
                        </nav>
                    </div>
                </div>                                    
                <div class="modal fade" id="editCodice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="max-width: 70%;"  >
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4>Modifica movimento</h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form class="col s12" id="form-edit-codice" action="javascript:realAssetsFramework.ModModal('form-edit-codice', '<?= WS_MODULE ?>')">
                                <!--Set dropzone height-->
                                <div class="modal-body">
                                    <input type="hidden" id="action"  name="action" value="save" />
                                    <input type="hidden" id="edit_ID" name="ID" />
                                    <div class="row form-group">
                                        <div class="col-4">
                                            <label><span class="text-danger">*&nbsp;</span>Tipo contenuto</label>
                                            <select required=""  class="form-control" id="edit_TIPO_CONTENUTO" name="TIPO_CONTENUTO">
                                                <?
                                                foreach ($tipo_contenuto as $value){
                                                ?>
                                                <option value="<?=$value['ID_CODICE']?>"><?=$value['DESCRIZIONE']?></option>
                                                <?}?>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label><span class="text-danger">*&nbsp;</span>Categoria</label>
                                            <select required=""  class="form-control" id="edit_CATEGORIA" name="CATEGORIA">
                                                <?
                                                foreach ($categoria as $value){
                                                ?>
                                                <option value="<?=$value['ID_CODICE']?>"><?=$value['DESCRIZIONE']?></option>
                                                <?}?>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label><span class="text-danger">*&nbsp;</span>Genere</label>
                                            <select required=""  class="form-control" id="edit_GENERE" name="GENERE">
                                                <?
                                                foreach ($genere as $value){
                                                ?>
                                                <option value="<?=$value['ID_CODICE']?>"><?=$value['DESCRIZIONE']?></option>
                                                <?}?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-6">
                                            <label><span class="text-danger">*&nbsp;</span>Titolo</label>                                        
                                            <input autocomplete="off" class="form-control" type="text" id="edit_TITOLO" required="" name="TITOLO" required>
                                        </div>
                                        <div class="col-6">
                                            <label><span class="text-danger">*&nbsp;</span>Sottotitolo</label>                                        
                                            <input autocomplete="off" class="form-control" type="text" id="edit_SOTTOTITOLO" required="" name="SOTTOTITOLO" required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-6">
                                            <label><span class="text-danger">*&nbsp;</span>Autore</label>                                        
                                            <input autocomplete="off" class="form-control" type="text" id="edit_AUTORE" required="" name="AUTORE" required>
                                        </div>
                                        <div class="col-6">
                                            <label><span class="text-danger">*&nbsp;</span>Ubicazione</label>                                        
                                            <input autocomplete="off" class="form-control" type="text" id="edit_UBICAZIONE" required="" name="UBICAZIONE" required>
                                        </div>
                                    </div>                                 
                                    <div class="row form-group">
                                        <label >NOTE</label>                                    
                                        <input autocomplete="off" class="form-control" type="text" id="edit_NOTE" name="NOTE">
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
                                <h4>Nuovo contenuto</h4>
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
                                        <div class="col-4">
                                            <label><span class="text-danger">*&nbsp;</span>Tipo contenuto</label>
                                            <select required=""  class="form-control" id="movTIPO_CONTENUTO" name="TIPO_CONTENUTO">
                                                <?
                                                foreach ($tipo_contenuto as $value){
                                                ?>
                                                <option value="<?=$value['ID_CODICE']?>"><?=$value['DESCRIZIONE']?></option>
                                                <?}?>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label><span class="text-danger">*&nbsp;</span>Categoria</label>
                                            <select required=""  class="form-control" id="movCATEGORIA" name="CATEGORIA">
                                                <?
                                                foreach ($categoria as $value){
                                                ?>
                                                <option value="<?=$value['ID_CODICE']?>"><?=$value['DESCRIZIONE']?></option>
                                                <?}?>
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <label><span class="text-danger">*&nbsp;</span>Genere</label>
                                            <select required=""  class="form-control" id="movGENERE" name="GENERE">
                                                <?
                                                foreach ($genere as $value){
                                                ?>
                                                <option value="<?=$value['ID_CODICE']?>"><?=$value['DESCRIZIONE']?></option>
                                                <?}?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-6">
                                            <label><span class="text-danger">*&nbsp;</span>Titolo</label>                                        
                                            <input autocomplete="off" class="form-control" type="text" id="movTITOLO" required="" name="TITOLO" required>
                                        </div>
                                        <div class="col-6">
                                            <label><span class="text-danger">*&nbsp;</span>Sottotitolo</label>                                        
                                            <input autocomplete="off" class="form-control" type="text" id="movSOTTOTITOLO" required="" name="SOTTOTITOLO" required>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-6">
                                            <label><span class="text-danger">*&nbsp;</span>Autore</label>                                        
                                            <input autocomplete="off" class="form-control" type="text" id="movAUTORE" required="" name="AUTORE" required>
                                        </div>
                                        <div class="col-6">
                                            <label><span class="text-danger">*&nbsp;</span>Ubicazione</label>                                        
                                            <input autocomplete="off" class="form-control" type="text" id="movUBICAZIONE" required="" name="UBICAZIONE" required>
                                        </div>
                                    </div>                                 
                                    <div class="row form-group">
                                        <label >NOTE</label>                                    
                                        <input autocomplete="off" class="form-control" type="text" id="NOTE" name="NOTE">
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
                                    <span class="navbar-brand mb-0 h1">Gestione della biblioteca</span>
                                </li>
                            </ul>
                            <span class="navbar-text">
                                <button class="btn btn-sm btn-success" type="button" onclick="" data-toggle="modal" data-target="#newModalCodice" title="Nuovo contenuto" ><i class="fas fa-laptop-code"></i> Nuova contenuto</button>                            
                            </span>
                        </nav> 
                        <table id="ListTesti" class="table table-striped table-bordered" style="width:100%">                                    
                            <thead>
                                <tr>
                                    <th>ID</th>                                    
                                    <th>TITOLO</th>
                                    <th>SOTTOTITOLO</th>
                                    <th>AUTORE</th>
                                    <th>CATEGORIA</th>
                                    <th>UBICAZIONE</th>
                                    <th>Gestione</th>                                    
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
 
                $('#ListTesti').DataTable({
                    /*initComplete: function () {
                        this.api().columns([1]).every(function () {
                            var column = this;
                                                    
                            var select = $('<select class="selectForPages"><option value="">Tutti i mesi</option></select>')
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
                                    select.append('<option value="' +  mesi[d] + '" selected="selected">' + d + '</option>');
                                } else {
                                    select.append('<option value="' +  mesi[d] + '">' + d + '</option>');
                                }
                            });

                            $(select).click(function (e) {
                                e.stopPropagation();
                            });

                        });                           
                    },*/
                     //dom: 'lBrtip', //lBfrtip
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    'ajax': {
                        'url': WS_CALL + '?module=<?= WS_MODULE ?>&action=list'
                    },
                    'columns': [
                        {data: 'ID'},
                        {data: 'TITOLO'},
                        {data: 'SOTTOTITOLO'},
                        {data: 'AUTORE'},
                        {data: 'CATEGORIA'},
                        {data: 'UBICAZIONE'},                        
                        {data: 'fn'}/*,
                         { data: 'invisibile' }*/
                    ]
                });


            });


        </script>
    </body>
</html>