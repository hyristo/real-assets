<!DOCTYPE html>
<?php
include '../../lib/api.php';
define('WS_MODULE', 'donazioni');
define("THIS_PERMISSION", array('CODICI_VARI'));
include_once ROOT.'/layout/include_permission.php';

$tipo_donazione = CodiciVari::Load(0, 'TIPO_DONAZIONE');
$pro_donazione = CodiciVari::Load(0, 'PRO_DONAZIONE');


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
                                <li class="breadcrumb-item active" aria-current="page">Gestione delle donazioni</li>
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
                                        <div class="col-6">                                            
                                        <label><span class="text-danger">*&nbsp;</span>Memory card</label>
                                            <input autocomplete="off" class="form-control" type="hidden" id="edit_ID_CARD" required="" name="ID_CARD" >
                                            <input autocomplete="off" class="form-control" type="text" id="edit_CARD" readonly  name="CARD" >
                                            
                                        </div>
                                        <div class="col-6">
                                            <label><span class="text-danger">*&nbsp;</span>Importo in euro</label>                                        
                                            <input autocomplete="off" class="form-control" type="number" id="edit_IMPORTO" required="" name="IMPORTO" required>
                                        </div>
                                    </div> 
                                    <div class="row form-group">
                                        <div class="col-6">
                                            <label for="MESE"><span class="text-danger">*&nbsp;</span>Mese</label>
                                            <select required="" class="form-control"  id="edit_MESE" name="MESE">
                                            <option value="">--</option>
                                                <?
                                                foreach ($MESI as $k => $value) {
                                                ?>
                                                <option value="<?=$k?>"><?=$value?></option>
                                                <?}?>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label for="ANNO"><span class="text-danger">*&nbsp;</span>Anno</label>
                                            <select required=""  class="form-control" id="edit_ANNO" name="ANNO">
                                                <?
                                                for ($i = date('Y')-1 ; $i<= date('Y')+1 ; $i++){
                                                ?>
                                                <option value="<?=$i?>"><?=$i?></option>
                                                <?}?>
                                            </select>
                                        </div>
                                    </div>                                                                
                                    <div class="row form-group">
                                        <div class="col-6">
                                        <label for="TIPO_DONAZIONE"><span class="text-danger">*&nbsp;</span>Tipo donazione</label>
                                        <select required=""  class="form-control" id="edit_TIPO_DONAZIONE" name="TIPO_DONAZIONE">
                                            <?
                                            foreach ($tipo_donazione as $value){
                                            ?>
                                            <option value="<?=$value['ID_CODICE']?>"><?=$value['DESCRIZIONE']?></option>
                                            <?}?>
                                        </select>
                                        </div>
                                        <div class="col-6">
                                            <label for="PRO_DONAZIONE"><span class="text-danger">*&nbsp;</span>Pro</label>
                                            <select required="" class="form-control" id="edit_PRO_DONAZIONE" name="PRO_DONAZIONE">
                                                <?
                                                foreach ($pro_donazione as $value){
                                                ?>
                                                <option value="<?=$value['ID_CODICE']?>"><?=$value['DESCRIZIONE']?></option>
                                                <?}?>
                                            </select>
                                        </div>
                                    </div>                                    
                                    <div class="row form-group">
                                        <label >NOTE</label>                                    
                                        <input autocomplete="off" class="form-control" type="text" id="edit_NOTE"  name="NOTE">
                                    
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
                                <h4>Nuova donazione</h4>
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
                                        <div class="col-6">
                                            <label><span class="text-danger">*&nbsp;</span>Memory Card</label>                                        
                                            <input autocomplete="off" class="form-control" type="hidden" id="ID_CARD" required="" name="ID_CARD" >
                                            <select style="width: 100%"  required="" class="form-control cfinput" id="MemoryCard" name="MemoryCard"></select>
                                        </div>
                                        <div class="col-6">
                                            <label><span class="text-danger">*&nbsp;</span>Importo in euro</label>                                        
                                            <input autocomplete="off" class="form-control" type="number" id="movIMPORTO" required="" name="IMPORTO" required>
                                        </div>
                                    </div> 
                                    <div class="row form-group">
                                        <div class="col-6">
                                            <label for="MESE"><span class="text-danger">*&nbsp;</span>Mese</label>
                                            <select required="" class="form-control" id="movMESE" name="MESE">
                                            <option value="">--</option>
                                                <?
                                                foreach ($MESI as $k => $value) {
                                                ?>
                                                <option value="<?=$k?>"><?=$value?></option>
                                                <?}?>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label for="ANNO"><span class="text-danger">*&nbsp;</span>Anno</label>
                                            <select required=""  class="form-control" id="movANNO" name="ANNO">
                                                <?
                                                for ($i = date('Y') ; $i<= date('Y')+2 ; $i++){
                                                ?>
                                                <option value="<?=$i?>"><?=$i?></option>
                                                <?}?>
                                            </select>
                                        </div>
                                    </div>                                                                
                                    <div class="row form-group">
                                        <div class="col-6">
                                        <label for="TIPO_DONAZIONE"><span class="text-danger">*&nbsp;</span>Tipo donazione</label>
                                        <select required=""  class="form-control" id="movTIPO_DONAZIONE" name="TIPO_DONAZIONE">
                                            <?
                                            foreach ($tipo_donazione as $value){
                                            ?>
                                            <option value="<?=$value['ID_CODICE']?>"><?=$value['DESCRIZIONE']?></option>
                                            <?}?>
                                        </select>
                                        </div>
                                        <div class="col-6">
                                            <label for="PRO_DONAZIONE"><span class="text-danger">*&nbsp;</span>Pro</label>
                                            <select required="" class="form-control" id="movPRO_DONAZIONE" name="PRO_DONAZIONE">
                                                <?
                                                foreach ($pro_donazione as $value){
                                                ?>
                                                <option value="<?=$value['ID_CODICE']?>"><?=$value['DESCRIZIONE']?></option>
                                                <?}?>
                                            </select>
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
                                    <span class="navbar-brand mb-0 h1">Gestione delle donazioni</span>
                                </li>
                            </ul>
                            <span class="navbar-text">
                                <button class="btn btn-sm btn-success" type="button" onclick="" data-toggle="modal" data-target="#newModalCodice" title="Nuov donazione" ><i class="fas fa-laptop-code"></i> Nuova donazione</button>                            
                            </span>
                        </nav> 
                        <table id="ListDonazioni" class="table table-striped table-bordered" style="width:100%">                                    
                            <thead>
                                <tr>
                                    <th>ID</th>                                    
                                    <th>MESE</th>
                                    <th>ANNO</th>
                                    <th>CARD</th>
                                    <th>IMPORTO</th>
                                    <th>PRO</th>
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

                $("#MemoryCard").select2({
                    language: "it",
                    minimumInputLength: 2,
                   // dropdownParent: $("#newModalCodice"),
                    ajax: {
                        url: WS_CALL + "?module=memory_card&action=search",                
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                searchTerm: params.term // search term
                            };
                        },
                        processResults: function (response) {
                            return {
                                results: response
                            };
                        },
                        cache: true
                    }
                });
                const mesi = [];
                mesi['Gennaio'] = 1;
                mesi['Febbraio'] = 2;
                mesi['Marzo'] = 3;
                mesi['Aprile'] = 4;
                mesi['Maggio'] = 5;
                mesi['Giugno'] = 6;
                mesi['Luglio'] = 7;
                mesi['Agosto'] = 8;
                mesi['Settembre'] = 9;
                mesi['Ottobre'] = 10;
                mesi['Novembre'] = 11;
                mesi['Dicembre'] = 12;    
                $('#ListDonazioni').DataTable({
                    initComplete: function () {
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
                    },
                     dom: 'lrtip', //lBfrtip
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    'ajax': {
                        'url': WS_CALL + '?module=<?= WS_MODULE ?>&action=list'
                    },
                    'columns': [
                        {data: 'ID'},
                        {data: 'MESE'},
                        {data: 'ANNO'},
                        {data: 'CARD'},
                        {data: 'IMPORTO'},
                        {data: 'PRO'},                        
                        {data: 'fn'}/*,
                         { data: 'invisibile' }*/
                    ]
                });


            });

            $('#MemoryCard').on('select2:select', function (e) {
                var data = e.params.data;
                if (data) {
                    $("#ID_CARD").val(data.id);
                } else {
                    functionSwall('error', "Selezionare un codice delle memory card nella lista sottostante!", 'error');
                    $("#ID_CARD").val("");
                }
            });


        </script>
    </body>
</html>