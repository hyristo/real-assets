<!DOCTYPE html>
<?php
include '../../lib/api.php';
define('WS_MODULE', 'persone');
define("THIS_PERMISSION", array('ANAG_PERSONE'));
include_once ROOT.'/layout/include_permission.php';


$tipo_persone = CodiciVari::Load(0, 'TIPO_PERSONA');
$sesso = CodiciVari::Load(0, 'SESSO');

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
                                <li class="breadcrumb-item active" aria-current="page">Gestione anagrafica fedeli</li>
                            </ol>
                        </nav>
                    </div>
                </div>                                    
                <div class="modal fade" id="editCodice" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="max-width: 70%;"  >
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4>Modifica anagrafica</h4>
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

                                        <div class="col-sm-4">
                                            <label ><span class="text-danger">*&nbsp;</span>COGNOME</label>
                                            <input autocomplete="off" class="form-control" type="text" id="edit_COGNOME" required="" name="COGNOME" required>
                                        </div>
                                        <div class="col-sm-4">
                                            <label ><span class="text-danger">*&nbsp;</span>NOME</label>
                                            <input autocomplete="off" class="form-control" type="text" id="edit_NOME" required="" name="NOME" required>
                                        </div>
                                        <div class="col-sm-4">
                                            <label ><span class="text-danger">*&nbsp;</span>CODICE FISCALE</label>
                                            <input autocomplete="off" class="form-control" type="text" id="edit_CODICE_FISCALE" required="" name="CODICE_FISCALE" required>
                                        </div>

                                    </div>
                                    <div class="row form-group">
                                        <div class="col-sm-4">
                                            <label><span class="text-danger">*&nbsp;</span>SESSO</label>
                                            <select class="form-control" id="edit_SESSO" placeholder="definisci il sesso" required="" name="SESSO" >
                                                <option value="" >Definisci il sesso</option>
                                                <? 
                                                foreach ($sesso as $value) { ?>
                                                    <option value="<?= $value['ID_CODICE'] ?>"><?= $value['DESCRIZIONE'] ?></option>
                                                <? } ?>
                                            </select>  
                                        </div>
                                        <div class="col-sm-4">
                                                <label for="DATA_NASCITA"><span class="text-danger">*&nbsp;</span>Data di nascita</label>
                                                <input type="date" required="" class="form-control" id="edit_DATA_NASCITA" name="DATA_NASCITA">
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="DATA_MORTE">Data di morte</label>
                                            <input type="date" class="form-control" id="edit_DATA_MORTE" name="DATA_MORTE">
                                        </div>
                                    </div>
                                    <div class="row row-group p-1">
                                        <div class="col-lg-3">
                                            <label for="COMUNE" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Comune</small></label>
                                            <select  style="width: 100%"  required=""  class="form-control cfinput" id="edit_COMUNE" name="COMUNE"></select>
                                        </div>
                                        <div class="col-lg-2">
                                            <label for="PROVINCIA" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Prov.</small></label>
                                            <input readonly placeholder="Prov." type="text" class="form-control cfinput"  required="" name="PROVINCIA" id="edit_PROVINCIA" value="">
                                        </div>
                                        <div class="col-lg-5">
                                            <label for="INDIRIZZO" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Indirizzo</small></label>
                                            <input   placeholder="Via" type="text" class="form-control cfinput"  required="" name="INDIRIZZO" id="edit_INDIRIZZO" value="">
                                        </div>
                                        <div class="col-lg-2">
                                            <label for="CIVICO" class="col-form-label"><small class="text-info">Civico</small></label>
                                            <input   placeholder="Civico" type="text" class="form-control cfinput" name="CIVICO" id="edit_CIVICO" value="">
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
                                <h4>Nuovo NOMINATIVO</h4>
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
                                        <div class="col-sm-4">
                                            <label ><span class="text-danger">*&nbsp;</span>COGNOME</label>
                                            <input autocomplete="off" class="form-control" type="text" id="COGNOME" required="" name="COGNOME" required>
                                        </div>
                                        <div class="col-sm-4">
                                            <label ><span class="text-danger">*&nbsp;</span>NOME</label>
                                            <input autocomplete="off" class="form-control" type="text" id="NOME" required="" name="NOME" required>
                                        </div>
                                        <div class="col-sm-4">
                                            <label ><span class="text-danger">*&nbsp;</span>CODICE FISCALE</label>
                                            <input autocomplete="off" class="form-control" type="text" id="CODICE_FISCALE" required="" name="CODICE_FISCALE" required>
                                        </div>

                                    </div>
                                    <div class="row form-group">
                                        <div class="col-sm-4">
                                            <label><span class="text-danger">*&nbsp;</span>SESSO</label>
                                            <select class="form-control" id="SESSO" placeholder="definisci il sesso" required="" name="SESSO" >
                                                <option value="" >Definisci il sesso</option>
                                                <? 
                                                foreach ($sesso as $value) { ?>
                                                    <option value="<?= $value['ID_CODICE'] ?>"><?= $value['DESCRIZIONE'] ?></option>
                                                <? } ?>
                                            </select>  
                                        </div>
                                        <div class="col-sm-4">
                                                <label for="DATA_NASCITA"><span class="text-danger">*&nbsp;</span>Data di nascita</label>
                                                <input type="date" required="" class="form-control" id="DATA_NASCITA" name="DATA_NASCITA">
                                        </div>
                                        <div class="col-sm-4">
                                            <label for="DATA_MORTE">Data di morte</label>
                                            <input type="date" class="form-control" id="DATA_MORTE" name="DATA_MORTE">
                                        </div>
                                    </div>
                                    <div class="row row-group p-1">
                                        <div class="col-lg-3">
                                            <label for="COMUNE" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Comune</small></label>
                                            <select  style="width: 100%"  required=""  class="form-control cfinput" id="COMUNE" name="COMUNE"></select>
                                        </div>
                                        <div class="col-lg-2">
                                            <label for="PROVINCIA" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Prov.</small></label>
                                            <input readonly placeholder="Prov." type="text" class="form-control cfinput"  required="" name="PROVINCIA" id="PROVINCIA" value="">
                                        </div>
                                        <div class="col-lg-5">
                                            <label for="INDIRIZZO" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Indirizzo</small></label>
                                            <input   placeholder="Via" type="text" class="form-control cfinput"  required="" name="INDIRIZZO" id="INDIRIZZO" value="">
                                        </div>
                                        <div class="col-lg-2">
                                            <label for="CIVICO" class="col-form-label"><small class="text-info">Civico</small></label>
                                            <input   placeholder="Civico" type="text" class="form-control cfinput" name="CIVICO" id="CIVICO" value="">
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
                                    <span class="navbar-brand mb-0 h1">Elenco anagrafica locatari</span>
                                </li>
                            </ul>
                            <span class="navbar-text">
                                <button class="btn btn-sm btn-success" type="button" onclick="" data-toggle="modal" data-target="#newModalCodice" title="Nuovo locatario" ><i class="fas fa-laptop-code"></i> Aggiungi</button>
                            </span>
                        </nav>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <table id="ListPersonale" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Codice fiscale</th>
                                            <th>Cognome</th>
                                            <th>Nome</th>
                                            <th>Data di nascita</th>
                                            <th>Modifica</th>
                                            <th>Cancella</th>
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

                $("#COMUNE").select2({
                    language: "it",
                    minimumInputLength: 3,
                    ajax: {
                        url: WS_CALL + "?module=account&action=searchComune",
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

                $("#edit_COMUNE").select2({
                    language: "it",
                    minimumInputLength: 3,
                    ajax: {
                        url: WS_CALL + "?module=account&action=searchComune",
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

                $('#ListPersonale').DataTable({
                    'processing': true,
                    'serverSide': true,
                    'serverMethod': 'post',
                    'ajax': {
                        'url': WS_CALL + '?module=<?= WS_MODULE ?>&action=list'
                    },
                    'columns': [
                        {data: 'ID'},
                        {data: 'CODICE_FISCALE'},
                        {data: 'COGNOME'},
                        {data: 'NOME'},
                        {data: 'DATA_NASCITA'},
                        {data: 'modifica'},
                        { data: 'cancellato' }
                    ]
                });


            });

            $('#COMUNE').on('select2:select', function (e) {
                var data = e.params.data;
                if (data) {
                    $("#PROVINCIA").val(data.codice_provincia);
                } else {
                    functionSwall('error', "Selezionare un Comune presente nella lista sottostante!", 'error');
                    $("#COMUNE").val("");
                }
            });
            $('#edit_COMUNE').on('select2:select', function (e) {
                var data = e.params.data;
                if (data) {
                    $("#edit_PROVINCIA").val(data.codice_provincia);
                } else {
                    functionSwall('error', "Selezionare un Comune presente nella lista sottostante!", 'error');
                    $("#edit_COMUNE").val("");
                }
            });


        </script>
    </body>
</html>