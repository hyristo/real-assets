

$(document).ready(function () {
    $('#loader').hide();
});
function createOperatore() {
    $('#loader').show();
    var form = document.getElementById('form-register');
    var formInvio = new FormData(form);
    formInvio.append('module', 'account')
    formInvio.append('action', 'save')
    postdata(WS_CALL, formInvio, function (response) {
        $('#loader').hide();
        var risp = jQuery.parseJSON(response);
        if (risp.esito === 1) {
            Swal.fire({
                type: 'success',
                title: 'OK',
                text: 'Operazione Avvenuta con successo!!',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK',
                allowOutsideClick: false
            }).then((result) => {
                if (result.value) {
                    location.reload();
                }
            })
        } else {
            functionSwall('error', risp.erroreDescrizione, '');
        }
    });
}
function editOperatore() {
    $('#loader').show();
    var form = document.getElementById('form-edit-operatore');
    var formInvio = new FormData(form);
    formInvio.append('module', 'account')
    formInvio.append('action', 'edit')
    postdata(WS_CALL, formInvio, function (response) {
        $('#loader').hide();
        var risp = jQuery.parseJSON(response);
        if (risp.esito === 1) {
            Swal.fire({
                type: 'success',
                title: 'OK',
                text: 'Operazione Avvenuta con successo!!',
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK',
                allowOutsideClick: false
            }).then((result) => {
                if (result.value) {
                    location.reload();
                }
            })
        } else {
            functionSwall('error', risp.erroreDescrizione, '');
        }
    });
}

function view_edit(ID) {
    postdata(WS_CALL + "?module=account&action=load&i=" + btoa(ID), "", function (response) {
        var m = JSON.parse(response);
        $.each(m, function (index, value) {
            $('#operatore_' + index).append(value);
            $('#operatore_' + index).val(value);
        });
    });
}
