$(document).ready(function () {
    $('#loader').hide();
    $("#luogo_nascita").autocomplete({
        source: WS_CALL + "?module=account&action=searchComune",
        minLength: 2,
        change: function (event, ui) {
            try {
                $("#luogo_nascita").val(ui.item.label);
                $("#prov_nascita").val(ui.item.codice_provincia);
                $("#cap_nascita").val(ui.item.cap);
            } catch (er) {
                console.log(ui);
                functionSwall('error', "Inserire un Comune presente nella lista sottostante!", 'error');
                $("#luogo_nascita").val("");
            }

        }
    });
    $("#comune_residenza").autocomplete({
        source: WS_CALL + "?module=account&action=searchComune",
        minLength: 2,
        change: function (event, ui) {
            try {
                $("#comune_residenza").val(ui.item.label);
                $("#prov_residenza").val(ui.item.codice_provincia);
                $("#cap_residenza").val(ui.item.cap);
            } catch (er) {
                console.log(ui);
                functionSwall('error', "Inserire un Comune presente nella lista sottostante!", 'error');
                $("#comune_residenza").val("");
            }

        }
    });
    $("#comune_istituto").autocomplete({
        source: WS_CALL + "?module=account&action=searchComune",
        minLength: 2,
        change: function (event, ui) {
            try {
                $("#comune_istituto").val(ui.item.label);
                $("#prov_istituto").val(ui.item.codice_provincia);
                $("#cap_istituto").val(ui.item.cap);
            } catch (er) {
                console.log(ui);
                functionSwall('error', "Inserire un Comune presente nella lista sottostante!", 'error');
                $("#comune_istituto").val("");
            }

        }
    });
});
function createLogin() {
    $('#loader').show();
    var risposta = codiceFISCALE($('#codice_fiscale').val());
    if (risposta) {
        var form = document.getElementById('form-register');
        var formInvio = new FormData(form);
        formInvio.append('module', 'account');
        formInvio.append('action', 'save');
        postdata(WS_CALL, formInvio, function (response) {
            $('#loader').hide();
            var risp = jQuery.parseJSON(response);
            if (risp.esito === 1) {
                functionSwall('success', 'Operazione Avvenuta con successo, riceverai a breve un\'email con il link per confermare la registrazione!!', BASE_HTTP + 'login.php');
            } else {
                functionSwall('error', risp.erroreDescrizione, '');
            }
        });
    } else {
        $('#loader').hide();
        functionSwall('error', "Il codice Fiscale inserito non è valido ", '');
    }
}

function loadDatiIS() {
    if ($('#seriale_istituzionale').val() != '' && $('#codice_meccanografico').val() != '') {
        $('#loader').show();
        var reqObj = {module: 'istituto', action: 'loadBy', SERIALE_ISTITUZIONALE: $('#seriale').val(), CODICE_MECCANOGRAFICO: $('#codice_meccanografico').val()};
        postdataClassic(WS_CALL, reqObj, function (response) {
            $('#loader').hide();
            var risp = jQuery.parseJSON(response);
            $('#denominazione').val(risp.DENOMINAZIONE);
            $('#email').val(risp.EMAIL);
            $('#pec').val(risp.PEC);
            $('#recapito_telefonico').val(risp.TELEFONO);
            $('#comune_istituto').val(risp.PEC);
            $('#comune_istituto').val(risp.COMUNE);
            $('#indirizzo_istituto').val(risp.INDIRIZZO);
            $('#tipologia_istituto').val(risp.TIPOLOGIA_ISTITUTO);
            $('#tipologia_istituto_text').val(risp.TIPOLOGIA_ISTITUTO_TEXT);
            $('#prov_istituto').val(risp.PROVINCIA_ISTITUTO);
            $('#cap_istituto').val(risp.CAP_ISTITUTO);
//            $('#categoria_istituto').val(risp.CATEGORIA_ISTITUTO);
//            $('#categoria_istituto_text').val(risp.CATEGORIA_ISTITUTO_TEXT);
        });
    }
//    $('input[name$="SERIALE_ISTITUZIONALE"]').val();
//    $('input[name$="CODICE_MECCANOGRAFICO"]').val();
}

//
//function addRegistrationJsp() {
//    $('#loader').show();
//    var email = $('#email').val();
//    var password = $('#password').val();
//    firebase.auth().createUserWithEmailAndPassword(email, password)
//            .then(function (result) {
//                var user = firebase.auth().currentUser;
//                logUser(user); // Optional
//                $('#loader').hide();
//                console.log(result);
//
//            }).catch(function (error) {
//        $('#loader').hide();
//        firebaseError(error.code);
//
//    });
//}
//function logUser(user) {
//    var form = document.getElementById('form-register');
//    var formInvio = new FormData(form);
//    formInvio.append('email', user.email);
//    formInvio.append('localId', user.uid);
//    postdata(WS_CALL + "?module=accountJsp&action=save", formInvio, function (response) {
//        $('#loader').hide();
//        var risp = jQuery.parseJSON(response);
//        if (risp.esito === 1) {
//            sendMailVerification(firebase);
//            functionSwall('success', 'Operazione Avvenuta con successo!!', BASE_HTTP + 'login.php');
//        } else {
//            //deleteUserFirebase(firebase);
//            functionSwall('error', risp.erroreDescrizione, '');
//        }
//    });
//
//}
//
//function changeView() {
//    var checkBox = document.getElementById("consenso_informativo");
//    // If the checkbox is checked, display the output text
//    if (checkBox.checked == true) {
//        $('#button_view').attr("disabled", false);
//    } else {
//        $('#button_view').attr("disabled", true);
//    }
//}
