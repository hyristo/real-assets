<!DOCTYPE html>
<?php
include '../../../lib/api.php';
define('WS_MODULE', 'patrimonio');
define("THIS_PERMISSION", array('CODICI_VARI'));
include_once ROOT.'/layout/include_permission.php';
$id_patrimonio = (isset($_POST['id_patrimonio']) ? intval($_POST['id_patrimonio']) : 0 );
$id_contratto = (isset($_POST['id']) ? intval($_POST['id']) : 0 );
$contratto = new Contratti($id_contratto);
$patrimonio = new AnagraficaPatrimonio($id_patrimonio);
$tipo_rata = CodiciVari::Load(0, 'TIPO_RATA');
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
                        <li class="breadcrumb-item"><a href="<?=HTTP_PRIVATE_SECTION?>dashboard_admin.php">Pagina iniziale</a></li>
                        <li class="breadcrumb-item"><a href="<?=HTTP_PRIVATE_SECTION?>patrimonio.php">Lista beni patrimoniali</a></li>
                        <li class="breadcrumb-item"><a href="javascript:redirectPatrimonio()"><?=$patrimonio->NOME?></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Contratto</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="anagrafica-tab" data-toggle="tab" href="#anagrafica" role="tab" aria-controls="anagrafica" aria-selected="true">Gestione contratto di locazione</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="rate-tab" data-toggle="tab" href="#rate" role="tab" aria-controls="rate" aria-selected="false">Rate</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="doc-tab" data-toggle="tab" href="#doc" role="tab" aria-controls="doc" aria-selected="false">Allegati</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="anagrafica" role="tabpanel" aria-labelledby="anagrafica-tab">
                        <form  id="form-contratto" class="needs-validation" action="javascript:saveContratto()">
                            <div class="card-body">
                                <input type="hidden" name="module" id="module">
                                <input type="hidden" name="action" id="action">
                                <input type="hidden" name="ID" id="ID" value="<?=$contratto->ID?>">
                                <input type="hidden" name="ID_PATRIMONIO" id="ID_PATRIMONIO" value="<?=$patrimonio->ID?>">
                                <input type="hidden" required name="ID_LOCATARIO" id="ID_LOCATARIO" value="<?=$contratto->ID_LOCATARIO?>">
                                <div class="row row-group p-1">
                                    <div class="col-lg-7">
                                        <label for="LOCATARIO" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Locatario</small></label>
                                        <select  style="width: 100%"  required=""  class="form-control cfinput" id="LOCATARIO" name="LOCATARIO"></select>
                                    </div>
                                    <div class="col-lg-2">
                                        <?
                                        if(intval($contratto->ID)<=0){
                                        ?>
                                        <label for="" class="col-form-label"><small class="text-info">Nuovo locatario</small></label><br/>
                                        <button id="BtnNewLocatario" class="btn btn-sm btn-success" type="button" onclick="" data-toggle="modal" data-target="#newModalCodice" title="Nuovo locatario" ><i class="fas fa-laptop-code"></i></button>
                                        <?}?>
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="NUMERO" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Numero</small></label>
                                        <input   placeholder="Numero del contratto" type="text" class="form-control cfinput"  required="" name="NUMERO" id="NUMERO" value="<?= $contratto->NUMERO ?>">
                                    </div>
                                </div>
                                <div class="row row-group p-1">
                                    <div class="col-lg-3">
                                        <label for="DATA_CONTRATTO" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Data contratto</small></label>
                                        <input   placeholder="Data di sottoscrizione del contratto" type="date" class="form-control cfinput"  required="" name="DATA_CONTRATTO" id="DATA_CONTRATTO" value="<?= $contratto->DATA_CONTRATTO ?>">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="TIPO_DURATA_CONTRATTO" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Tipo durata</small></label>
                                        <select style="width: 100%" required="" class="form-control"  id="TIPO_DURATA_CONTRATTO" name="TIPO_DURATA_CONTRATTO">
                                            <option value="">--</option>
                                            <?
                                            foreach ($tipo_rata as $value){
                                                ?>
                                                <option value="<?=$value['SIGLA']?>" <?=($contratto->TIPO_DURATA_CONTRATTO == $value['SIGLA'] ? 'selected' : '')?>><?=$value['DESCRIZIONE']?></option>
                                            <?}?>
                                        </select>
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="DURATA_CONTRATTO" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Durata</small></label>
                                        <input   placeholder="" type="number" class="form-control cfinput" min="1" required="" name="DURATA_CONTRATTO" id="DURATA_CONTRATTO" value="<?= $contratto->DURATA_CONTRATTO ?>">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="DATA_TERMINE" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Data termine</small></label>
                                        <input  readonly placeholder="Data di termine del contratto" type="date" class="form-control cfinput"  required="" name="DATA_TERMINE" id="DATA_TERMINE" value="<?= $contratto->DATA_TERMINE ?>">
                                    </div>

                                </div>
                                <div class="row row-group p-1">
                                    <div class="col-lg-3">
                                        <label for="IMPORTO" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Importo contratto</small></label>
                                        <input   placeholder="" type="number" class="form-control cfinput" min="1" required="" name="IMPORTO" id="IMPORTO" value="<?= $contratto->IMPORTO ?>">
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="TIPO_RATA" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Tipo rata</small></label>
                                        <select style="width: 100%" required="" class="form-control"  id="TIPO_RATA" name="TIPO_RATA">
                                            <option value="">--</option>
                                            <?
                                            foreach ($tipo_rata as $value){
                                                ?>
                                                <option value="<?=$value['SIGLA']?>" <?=($contratto->TIPO_RATA == $value['SIGLA'] ? 'selected' : '')?>><?=$value['DESCRIZIONE']?></option>
                                            <?}?>
                                        </select>
                                    </div>
                                    <div class="col-lg-3">
                                        <label for="IMPORTO_RATA" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Importo rata</small></label>
                                        <input   placeholder="" type="number" class="form-control cfinput" min="1" required="" name="IMPORTO_RATA" id="IMPORTO_RATA" value="<?= $contratto->IMPORTO_RATA ?>">
                                    </div>

                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row ">
                                    <div class="offset-sm-2 col-sm-10 text-right ">
                                        <button style="display: none;" type="button" id="btn-refresh"  class="btn btn-md btn-danger" onclick="redirectPatrimonio()"><i class="fas fa-sync"></i>&nbsp;Annulla modifiche</button>
                                        <button type="submit" id="btn-save-patrimonio" class="btn btn-md btn-success"><i class="fas fa-save"></i>&nbsp;Salva</button>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="rate" role="tabpanel" aria-labelledby="rate-tab">

                        <?php
                        if($contratto->ID>0){
                            include "elenco_rate.php";
                        }else{
                            echo "<h5>Salvare il bene prima di visualizzare le rate generate</h5>";
                        }
                        ?>

                    </div>
                    <div class="tab-pane fade" id="doc" role="tabpanel" aria-labelledby="doc-tab">

                        <?php
                        if($contratto->ID>0){
                            include "allegati_contratto.php";
                        }else{
                            echo "<h5>Salvare il bene prima di visualizzare le rate generate</h5>";
                        }
                        ?>

                    </div>
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
                    <form class="col s12" id="form-add-codice" action="javascript:realAssetsFramework.ModModal('form-add-codice', 'persone', setNewLocatario)">
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



    </div>

</main>
<!-- BEGIN: Footer-->
<?php include_once ROOT . 'layout/footer.php'; ?>
<!-- END: Footer-->
<script type="text/javascript">
    var isModified = false;
    var id_patrimonio = '<?=$id_patrimonio?>';
    var id_contratto = '<?=$id_contratto?>';
    var dt;
    var dtRate;
    $(document).ready(function () {
        setReadOnly();
        //listFoto();
        //listContratti();

        $('#loader').hide();

        if (id_contratto > 0) {
            listRate(false);
            listAllegati(false);
        }



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

        $("#LOCATARIO").select2({
            language: "it",
            minimumInputLength: 3,
            ajax: {
                url: WS_CALL + "?module=persone&action=search",
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
        <? if ($contratto->ID_LOCATARIO > 0) {
        $descrizione = $contratto->locatario->COGNOME. ' ' . $contratto->locatario->NOME . ' (' .$contratto->locatario->CODICE_FISCALE .')';
            ?>
        $("#LOCATARIO").select2("trigger", "select", {
            data: {
                id: '<?= addslashes($contratto->ID_LOCATARIO) ?>',
                text: '<?= addslashes($descrizione) ?>',
                label: '<?= addslashes($descrizione) ?>'
            }
        });
        <?}?>

        $("#DATA_CONTRATTO").on('change', function (){
            calcolaTermine();
        });
        $("#TIPO_DURATA_CONTRATTO").on('change', function (){
            calcolaTermine();
        });
        $("#DURATA_CONTRATTO").on('change', function (){
            calcolaTermine();
        });
        $("#IMPORTO").on('change', function (){
            calcolaRata();
        });
        $("#TIPO_RATA").on('change', function (){
            calcolaRata();
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



    });

    function calcolaTermine(){
        var d = $("#TIPO_DURATA_CONTRATTO").val();
        var n = $("#DURATA_CONTRATTO").val();
        var inizio = $("#DATA_CONTRATTO").val();
        if(inizio !="" && d != "" && n > 0){
            var controllo = moment(inizio).add(n, d).format('YYYY-MM-DD');
            $("#DATA_TERMINE").val(controllo);
        }
    }

    function calcolaRata(){
        var importoC = $("#IMPORTO").val();
        var d = $("#TIPO_RATA").val();
        var rata = 0;
        switch (d) {
            case 'M':
                rata = (importoC/12);
                break;
            case 'Y':
                rata = (importoC/1);
                break;
        }
        $('#IMPORTO_RATA').val(parseFloat(rata));
    }


    $('#LOCATARIO').on('select2:select', function (e) {
        var data = e.params.data;
        if (data) {
            $("#ID_LOCATARIO").val(data.id);
            $("#BtnNewLocatario").attr('disabled', true);
        } else {
            functionSwall('error', "Selezionare un'anagrafica presente nella lista sottostante!", 'error');
            $("#ID_LOCATARIO").val('');
            $("#BtnNewLocatario").removeAttr('disabled');
        }
    });

    function setNewLocatario(rec){
        $("#ID_LOCATARIO").val(rec.lastId);
        $("#BtnNewLocatario").attr('disabled', true);
        $("#LOCATARIO").select2("trigger", "select", {
            data: {
                id: rec.lastId,
                text: rec.rec,
                label: rec.rec
            }
        });
        $('#newModalCodice').modal('hide');
    }


    function verifyDateField(t) {
        var arr1 = t[0].min.split("-");
        var arr2 = t[0].max.split("-");
        var arr3 = t.val().split("-");
        var d1 = new Date(arr1[0], arr1[1] - 1, arr1[2]);
        var d2 = new Date(arr2[0], arr2[1] - 1, arr2[2]);
        var d3 = new Date(arr3[0], arr3[1] - 1, arr3[2]);
        var r1 = d1.getTime();
        var r2 = d2.getTime();
        var r3 = d3.getTime();
        if (Number.isNaN(r3)) {
            t.removeClass('is-valid').addClass('is-invalid');
        } else {
            if (r1 > r3 || r3 > r2) {
                t.removeClass('is-valid').addClass('is-invalid');
            } else {
                t.removeClass('is-invalid').addClass('is-valid');
            }
        }
    }


    function setReadOnly() {
        $('#form-contratto :input').each(function () {

            $(this).change(function () {
                isModified = true;
                if ($(this)[0].type === 'date') {
                    verifyDateField($(this));
                } else {

                    $(this).addClass('is-valid');
                }
                $('#btn-refresh').show();

            });
        });

    }

    function saveContratto() {

        $('#loader').show();
        var form = document.getElementById('form-contratto');

        var formInvio = new FormData(form);
        formInvio.set('module', 'contratti');
        formInvio.set('action', 'saveContratto');

        postdata(WS_CALL, formInvio, function (response) {
            $('#loader').hide();
            var risp = jQuery.parseJSON(response);
            if (risp.esito === 1) {
                isModified = false;
                Swal.fire({
                    type: 'success',
                    title: 'OK',
                    text: "Operazione completata con successo",
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false
                }).then((result) => {
                    redirectPatrimonio();
                });

            } else {
                functionSwall('error', risp.erroreDescrizione, '');
            }
        });


    }

    function resetModifiche() {
        var obj = {
            id_patrimonio: id_patrimonio
        };
        if (isModified) {
            Swal.fire({
                title: "Attenzione",
                text: "Sicuro di volere procedere? Eventuali modifiche non salvate andranno perse.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.value) {
                    $.redirect(HTTP_PRIVATE_SECTION + "inc_patrimonio/page_contratto.php", obj);
                }
            });
        } else {
            $.redirect(HTTP_PRIVATE_SECTION + "inc_patrimonio/page_contratto.php", obj);
        }
    }

    function redirectPatrimonio() {
        var obj = {
            id: id_patrimonio
        };
        if (isModified) {
            Swal.fire({
                title: "Attenzione",
                text: "Sicuro di volere procedere? Eventuali modifiche non salvate andranno perse.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.value) {
                    $.redirect(HTTP_PRIVATE_SECTION + "page_patrimonio.php", obj);
                }
            });
        } else {
            $.redirect(HTTP_PRIVATE_SECTION + "page_patrimonio.php", obj);
        }
    }
    function goBack() {
        if (isModified) {
            Swal.fire({
                title: "Attenzione",
                text: "Sicuro di volere procedere? Eventuali modifiche non salvate andranno perse.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            }).then((result) => {
                if (result.value) {

                    $.redirect(HTTP_PRIVATE_SECTION + "page_patrimonio.php");
                }
            });
        } else {
            $.redirect(HTTP_PRIVATE_SECTION + "page_patrimonio.php");
        }
    }

    function listRate(reload){
        var dtRate = $('#listRate');

        if(!reload){
            dtRate.DataTable({
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                dom: 'tip',//lBfrtip
                'ajax': {
                    'url': WS_CALL,
                    'data': {
                        'module': 'contratti',
                        'action': 'list_rate',
                        'id_contratto': id_contratto,
                        'length': 10
                    }
                },
                'columnDefs': [
                    {'width': '180px', 'targets': 2}
                ],
                'columns': [
                    {data: 'ID', "visible": false},
                    {data: 'DATA_SCADENZA', "visible": true, orderable: false},
                    {data: 'IMPORTO_RATA', "visible": true},
                    {data: 'RATA_PAGATA', "visible": true},
                    {data: 'OP', orderable: false}
                ],
                'initComplete': function (settings, json) {
                    //dt.rows( ':eq(0)' ).select();
                }
            });
        }else{
            dtRate.DataTable().ajax.reload();
        }

    }

    function pagaRata(){
        Swal.fire({
            title: "Attenzione",
            text: "Sicuro di volere procedere con il pagamento della rata?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.value) {

            }
        });
    }

    function listAllegati(reload){
        var dt = $("#ListAllegati");

        if(!reload) {
            dt.DataTable({
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                dom: 'tip',//lBfrtip
                'ajax': {
                    'url': WS_CALL,
                    'data': {
                        'module': 'contratti',
                        'action': 'listAllegati',
                        'id_contratto': id_contratto,
                        'length': 10
                    }
                },
                'columnDefs': [
                    {'width': '180px', 'targets': 2}
                ],
                'columns': [
                    {data: 'ID', "visible": false},
                    {data: 'DESCRIZIONE', "visible": true, orderable: false},
                    {data: 'PATH', "visible": false},
                    {data: 'OP', orderable: false}
                ]
            });
        }else{
            dt.DataTable().ajax.reload();
        }
    }

    function saveDocumenti(){
        var file = document.getElementById("documento_doc");
        console.log(file);
        var maxsize = (MAX_SIZE_FILE_UPLOAD);
        var dimensionetrovata = file.files[0].size;
        if (dimensionetrovata > maxsize) {
            functionSwall('error', "La dimensione del file allegato eccede i limiti massimi previsti", "");
        } else {
            $('#loader').show();
            var form = document.getElementById('form-allegati');
            var formInvio = new FormData(form);
            formInvio.set('module', 'contratti');
            formInvio.set('action', 'saveAllegato');
            postdata(WS_CALL, formInvio, function (response) {
                $('#loader').hide();
                var risp = jQuery.parseJSON(response);
                if (risp.esito === 1) {
                    $('#loader').hide();
                    Swal.fire({
                        type: 'success',
                        title: 'OK',
                        text: "Operazione completata con successo",
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.value) {
                            $('#allegatiModal').modal('toggle');
                            listAllegati(true);
                        }
                    });
                } else {
                    $('#loader').hide();
                    functionSwall('error', risp.erroreDescrizione, "");
                }
            });
        }
    }

    function openStreamer(id) {
        var object = {
            id: id,
            module: 'streamerFile',
            action: 'download'
        };
        $.redirect(WS_CALL, object, "POST");
    }








</script>
</body>
</html>