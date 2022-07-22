$(document).ready(function () {
    $('.carico').hide();
    $('.scarico').hide();
    $('#TIPO_OPERAZIONE').on('change', function (e) {
        var str = "";
        $("#TIPO_OPERAZIONE option:selected").each(function () {
            str += $(this).text();
        });
        if (str === "CARICO") {
            $('.carico').show();
            $('.scarico').hide();
            $('#PAESE_PROVENIENZA').attr('required');
            $('#PAESE_DESTINAZIONE').removeAttr('required');
            $('#PAESE_PROVENIENZA').val('');
            $('#PAESE_DESTINAZIONE').val('');
        } else if (str === "SCARICO") {
            $('.carico').hide();
            $('.scarico').show();
            $('#PAESE_DESTINAZIONE').attr('required');
            $('#PAESE_PROVENIENZA').removeAttr('required');
            $('#PAESE_PROVENIENZA').val('');
            $('#PAESE_DESTINAZIONE').val('');
        } else {
            $('.carico').hide();
            $('.scarico').hide();
        }
    });
    $("#SPECIE_VEGETALE").on('change', function (e) {
        $("#NOME_BOTANICO_V").val("");
        $("#NOME_BOTANICO").val("");
        var str = "";
        $("#SPECIE_VEGETALE option:selected").each(function () {
            str += $(this).text();
        });
        $("#NOME_BOTANICO_V").autocomplete({
            source: WS_CALL + '?module=registro_tracciabilita&action=searchbotanic&tipologia=' + str,
            minLength: 1,
            change: function (event, ui) {
                try {
                    $("#NOME_BOTANICO_V").val(ui.item.label);
                    $("#NOME_BOTANICO").val(ui.item.label);
                } catch (er) {
                    functionSwall('error', "Inserire un nome valido!", '');
                    ;
                    $("#NOME_BOTANICO_V").val("");
                    $("#NOME_BOTANICO").val("");
                }
            }
        });

    });
});


function openStreamer(value)
{
    var object = {
        value: value,
        module: 'streamerFile',
        action: 'download',
        tipo_download: 'reg'
    };
    $.redirect(WS_CALL, object, "POST");
}




function saveMovimento() {
    $('#loader').show();
    var form = document.getElementById('add-form-movimento');
    var formInvio = new FormData(form);
    formInvio.append('module', 'registro_tracciabilita');
    formInvio.append('action', 'save');
    postdata(WS_CALL, formInvio, function (response) {
        $('#loader').hide();
        var risp = jQuery.parseJSON(response);
        if (risp.esito === 1) {
            functionSwall('success', 'Operazione Effettuata con successo', HTTP_PRIVATE_SECTION + 'registro_tracciabilita', risp.cuaa);
        } else {
            functionSwall('error', risp.erroreDescrizione, '');
        }
    });
}
function goMovimento(azienda, prg_scheda) {
    var object = {
        azienda: azienda,
        prg_scheda: prg_scheda
    };
    $.redirect("registro_tracciabilita/registro_tracciabilita_add.php", object);
}
function editMovimento(id, azienda) {
    var object = {
        id: id,
        azienda: azienda
    };
    $.redirect("registro_tracciabilita/registro_tracciabilita_edit.php", object);
}
//function deleteDati(id) {
//    var object = {
//        id: id,
//        module: 'magazzino',
//        action: 'deletelogical'
//    };
//    Swal.fire({
//        title: "Attenzione",
//        text: "Vuoi eliminare il magazzino  selezionato?",
//        icon: 'warning',
//        showCancelButton: true,
//        confirmButtonColor: '#3085d6',
//        cancelButtonColor: '#d33',
//        confirmButtonText: 'OK'
//    }).then((result) => {
//        if (result.value) {
//            $('#loader').show();
//            postdataClassic(WS_CALL, object, function (response) {
//                var risp = jQuery.parseJSON(response);
//                if (risp.esito === 1) {
//                    $('#loader').hide();
//                    Swal.fire({
//                        type: 'success',
//                        title: 'OK',
//                        text: "Operazione completata con successo",
//                        showCancelButton: false,
//                        confirmButtonColor: '#3085d6',
//                        confirmButtonText: 'OK',
//                        allowOutsideClick: false
//                    }).then((result) => {
//                        loadDatatable();
//                    });
//                } else {
//                    $('#loader').hide();
//                    functionSwall('error', risp.erroreDescrizione, "");
//                }
//            });
//        }
//    });
//}

function editRegistro(id) {
    $('#loader').show();
    var object = {
        id: id,
        module: 'registro_tracciabilita',
        action: 'load'
    };
    postdataClassic(WS_CALL, object, function (response) {
        $('#loader').hide();
        var risp = jQuery.parseJSON(response);
        Object.entries(risp).forEach(([key, value]) => {
            if (key == "PATH_FILE" && (value != "" && value !== null)) {
                $('#eliminaVisuale').show();
                $('#fileCaricamento').hide();
                var btn = document.createElement("button");
                btn.setAttribute("type", "button");
                btn.innerHTML = "Download File";
                btn.setAttribute("class", "btn btn-info");
                $('#button').attr("button");

                btn.onclick = function () {
                    openStreamer(value);
                };
                $('#bodyFile').append(btn);
                var btn1 = document.createElement("button");
                btn1.setAttribute("type", "button");
                btn1.setAttribute("class", "btn btn-danger");
                btn1.innerHTML = "Elimina ";
                $('#button').attr("button");
                btn1.onclick = function () {
                    deleteFile(value, id);
                };
                $('#bodyFileDelete').append(btn1);
            }


            $("#" + key).val(value);
            if (key == "NOME_BOTANICO") {
                $("#" + key + "_V").val(value);
            }
            if (key == "TIPO_OPERAZIONE" && value == 1) {
                $('.carico').show();
                $('.scarico').hide();
                $('#paese_provenienza').attr('required');
                $('#paese_destinazione').removeAttr('required');
            } else if (key == "TIPO_OPERAZIONE" && value == 2) {
                $('.carico').hide();
                $('#paese_destinazione').attr('required');
                $('#paese_provenienza').removeAttr('required');
                $('.scarico').show();
        }
        });
    });
}