<!DOCTYPE html>
<?php
include '../../lib/api.php';
define('WS_MODULE', 'patrimonio');
define("THIS_PERMISSION", array('CODICI_VARI'));
include_once ROOT.'/layout/include_permission.php';
$id_patrimonio = (isset($_POST['id']) ? intval($_POST['id']) : 0 );
$patrimonio = new AnagraficaPatrimonio($id_patrimonio);
$tipo_patrimonio = CodiciVari::Load(0, 'TIPO_PATRIMONIO');

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
                        <li class="breadcrumb-item"><a href="dashboard_admin.php">Pagina iniziale</a></li>
                        <li class="breadcrumb-item"><a href="patrimonio.php">Lista beni patrimoniali</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Bene patrimoniale</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="anagrafica-tab" data-toggle="tab" href="#anagrafica" role="tab" aria-controls="anagrafica" aria-selected="true">Anagrafica</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="foto-tab" data-toggle="tab" href="#foto" role="tab" aria-controls="foto" aria-selected="false">Foto</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Contratti</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="anagrafica" role="tabpanel" aria-labelledby="anagrafica-tab">
                        <?php
                        include "inc_patrimonio/anagrafica_patrimonio.php";
                        ?>
                    </div>
                    <div class="tab-pane fade" id="foto" role="tabpanel" aria-labelledby="foto-tab">
                        <?php
                        if($patrimonio->ID>0){
                            include "inc_patrimonio/foto_patrimonio.php";
                        }else{
                            echo "<h5>Salvare il bene prima di procedere con le foto</h5>";
                        }
                        ?>

                    </div>
                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                        <?php
                        if($patrimonio->ID>0){
                            include "inc_patrimonio/elenco_contratti.php";
                        }else {
                            echo "<h5>Salvare il bene prima di procedere con il caricamento dei contratti</h5>";
                        }
                        ?>
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
    var isModified = false;
    var id_patrimonio = '<?=$id_patrimonio?>';
    var dt;
    $(document).ready(function () {
        setReadOnly();
        //listFoto();
        listContratti();
        $('#loader').hide();


        dt = $("#ListFoto").DataTable({
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            select: {
                style: 'single'
            },
            dom: 'tip',//lBfrtip
            'ajax': {
                'url': WS_CALL,
                'data': {
                    'module': 'patrimonio',
                    'action': 'listFoto',
                    'id_patrimonio': id_patrimonio,
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
            ],
            'initComplete': function (settings, json) {
                dt.rows( ':eq(0)' ).select();
            }
        });

        dt.on( 'select', function ( e, dt, type, indexes ) {
            var rowData = dt.rows( indexes ).data().toArray();
            $('#viewFoto').empty();
            var img = $(document.createElement('img'));
            img.addClass('card-img-top');
            img.attr('src', BASE_HTTP+'upload/'+UPLOAD_FOTO+id_patrimonio+'/'+ROOT_UPLOAD_PHOTO+rowData[0].PATH);
            var div = $(document.createElement('div'));
            div.addClass('card-body');
            var p = $(document.createElement('p'));
            p.addClass('card-text');
            p.append(rowData[0].DESCRIZIONE);
            div.append(p);
            $('#viewFoto').append(img);
            $('#viewFoto').append(div);
        }).on( 'deselect', function ( e, dt, type, indexes ) {
            $('#viewFoto').empty();
            var div = $(document.createElement('div'));
            div.addClass('card-body');
            var p = $(document.createElement('p'));
            p.addClass('card-text');
            p.append('Selezionare una riga dalla griglia per visualizzare la relativa immagine');
            div.append(p);
            $('#viewFoto').append(div);
        });




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
        <? if ($patrimonio->COMUNE != "") { ?>
        $("#COMUNE").select2("trigger", "select", {
            data: {
                id: '<?= addslashes($patrimonio->COMUNE) ?>',
                text: '<?= addslashes($patrimonio->COMUNE) ?>',
                codice_provincia: '<?= $patrimonio->PROVINCIA ?>'
            }
        });
        <? } ?>


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
        $('#form-patrimonio :input').each(function () {

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

    function savePatrimonio() {

        $('#loader').show();
        var form = document.getElementById('form-patrimonio');

        var formInvio = new FormData(form);
        formInvio.set('module', 'patrimonio');
        formInvio.set('action', 'savePatrimonio');

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
                    redirectPatrimonio(risp.lastId);
                });

            } else {
                functionSwall('error', risp.erroreDescrizione, '');
            }
        });


    }

    function redirectPatrimonio(id_patrimonio) {
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

    function goToContratto(id) {
        var obj = {
            id: id,
            id_patrimonio: id_patrimonio,
        };
        $.redirect(HTTP_PRIVATE_SECTION + "inc_patrimonio/page_contratto.php", obj);
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
                    $.redirect(HTTP_PRIVATE_SECTION + "patrimonio.php");
                }
            });
        } else {
            $.redirect(HTTP_PRIVATE_SECTION + "patrimonio.php");
        }
    }

    function listFoto(reload) {

        $("#ListFoto").DataTable().ajax.reload(function (json) {
            dt.rows( ':eq(0)' ).select();
        });
    }

    function saveFoto(){
        var file = document.getElementById("documento_foto");
        console.log(file);
        var maxsize = (MAX_SIZE_FILE_UPLOAD);
        var dimensionetrovata = file.files[0].size;
        if (dimensionetrovata > maxsize) {
            functionSwall('error', "La dimensione del file allegato eccede i limiti massimi previsti", "");
        } else {
            $('#loader').show();
            var form = document.getElementById('form-allegati');
            var formInvio = new FormData(form);
            formInvio.set('module', 'patrimonio');
            formInvio.set('action', 'saveFoto');
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
                            listFoto(true);
                        }
                    });
                } else {
                    $('#loader').hide();
                    functionSwall('error', risp.erroreDescrizione, "");
                }
            });
        }
    }

    function listContratti(reload){
        var dtContratti = $('#listContratti');

        if(!reload){
            dtContratti.DataTable({
                'processing': true,
                'serverSide': true,
                'serverMethod': 'post',
                dom: 'tip',//lBfrtip
                'ajax': {
                    'url': WS_CALL,
                    'data': {
                        'module': 'contratti',
                        'action': 'list',
                        'id_patrimonio': id_patrimonio,
                        'length': 10
                    }
                },
                'columnDefs': [
                    {'width': '180px', 'targets': 2}
                ],
                'columns': [
                    {data: 'ID', "visible": false},
                    {data: 'NUMERO', "visible": true, orderable: false},
                    {data: 'LOCATARIO', "visible": true},
                    {data: 'DATA_CONTRATTO', "visible": true},
                    {data: 'DATA_TERMINE', "visible": true},
                    {data: 'IMPORTO', "visible": false},
                    {data: 'OP', orderable: false}
                ],
                'initComplete': function (settings, json) {
                    //dt.rows( ':eq(0)' ).select();
                }
            });
        }else{
            dtContratti.DataTable().ajax.reload();
        }
    }



</script>
</body>
</html>