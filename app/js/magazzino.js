$(document).ready(function () {
    $("#magazzinoCOMUNE").autocomplete({
        source: WS_CALL + "?module=magazzino&action=searchComune",
        minLength: 2,
        appendTo:'#exampleModalCenter',
        change: function (event, ui) {
            try {
                $("#magazzinoCOMUNE").val(ui.item.label);
            } catch (er) {
                functionSwall('error', "Inserire un Comune presente nella lista sottostante!", 'error');
                $("#magazzinoCOMUNE").val("");
            }

        }
    });
});
function saveMagazzino() {
    $('#loader').show();
    var form = document.getElementById('add-form-magazzino');
    var formInvio = new FormData(form);
    formInvio.append('module', 'magazzino');
    formInvio.append('action', 'save');
    postdata(WS_CALL, formInvio, function (response) {
        $('#loader').hide();
        var risp = jQuery.parseJSON(response);
        if (risp.esito === 1) {
            Swal.fire({
                type: 'success',
                title: 'OK',
                text: "Operazione completata con successo",
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK',
                allowOutsideClick: false
            }).then((result) => {
                if(risp.action == 'INSERT'){
                    loadMagazzino(risp.lastId);
                }else{
                    loadMagazzino();
                }
            });
        } else {
            functionSwall('error', risp.erroreDescrizione, '');
        }
    });
}


function deleteDati(id) {
    var object = {
        id: id,
        module: 'magazzino',
        action: 'deletelogical'
    };
    Swal.fire({
        title: "Attenzione",
        text: "Vuoi eliminare il magazzino  selezionato?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'OK'
    }).then((result) => {
        if (result.value) {
            $('#loader').show();
            postdataClassic(WS_CALL, object, function (response) {
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
                        loadMagazzino();
                    });
                } else {
                    $('#loader').hide();
                    functionSwall('error', risp.erroreDescrizione, "");
                }
            });
        }
    });
}