$(document).ready(function () {
});

var summernoteconfigtestuale = [
    ['style', ['style']],
    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
    ['fontname', ['fontname']],
    ['fontsize', ['fontsize']],
    ['color', ['color']],
    ['para', ['ol', 'ul', 'paragraph', 'height']],
    ['table', ['table']],
    ['insert', ['link']],
    ['view', ['undo', 'redo', 'fullscreen', 'codeview', 'help']]
];

var datatableOptionsBasic = {
    dom: 'Bfrtip',
    iDisplayLength: 10,
    ordering: false,
    buttons: [
        'copy', 'csv', 'excel', 'pdf', 'print'
    ]
};
var optiondatepicker = {
    singleDatePicker: true,
    showDropdowns: true,
    drops: 'up',
    autoUpdateInput: false,
    locale: {
        format: 'DD-MM-YYYY'
    }
};
var optiondatepickerora = {
    singleDatePicker: true,
    showDropdowns: true,
    autoUpdateInput: false,
    timePicker: true,
    timePickerIncrement: 30,
    locale: {
        format: 'DD-MM-YYYY H:mm'
    }
};

var optiondaterangepicker = {
    showDropdowns: true,
    autoUpdateInput: false,
    locale: {
        format: 'DD-MM-YYYY'
    }
}

function loader(modalita) {
    if (modalita === "show") {
        $.blockUI({
            message: '<div class="ft-refresh-cw icon-spin font-medium-2"></div>',
            overlayCSS: {
                backgroundColor: '#FFF',
                opacity: 0.8,
                cursor: 'wait'
            },
            css: {
                border: 0,
                padding: 0,
                backgroundColor: 'transparent'
            }
        });
    } else if (modalita === "hide") {
        $.unblockUI();

    }
}

function postdataClassic(url, oggetto, callback) {
    $.ajax({
        url: url,
        method: "POST",
        data: oggetto,
        success: callback,
        error: function (reason, xhr) {
            console.log("error in processing your request", reason);
        }
    });
}
function postdata(url, oggetto, callback) {
    $.ajax({
        url: url,
        method: "POST",
        data: oggetto,
        processData: false,
        contentType: false,
        success: callback,
        error: function (reason, xhr) {
            console.log("error in processing your request", reason);
        }
    });
}
function getdata(url, callback) {
    $.ajax({
        url: url,
        method: "POST",
        success: callback,
        error: function (reason, xhr) {
            console.log("error in processing your request", reason);
        }
    });
}


function resettaform(idform) {
    console.log("chiedo un reset" + idform);
    $("#" + idform)[0].reset();
    console.log("fatto");
}


function swalfunction(mode, testo, reinvio) {
    if (mode == "success") {
        var scrivotesto = "";
        if (testo != "") {
            scrivotesto = testo;
        }
        swal({
            title: "Operazione conclusa con successo",
            text: scrivotesto,
            type: "success",
            showCancelButton: false,
            confirmButtonColor: "#31aa47",
            confirmButtonText: "OK",
            closeOnConfirm: true,
            closeOnCancel: false
        }
        , function (isConfirm) {
            if (isConfirm) {
                if (reinvio == "restoqui") {
                    location.reload();
                } else if (reinvio == "nonreload") {

                } else {
                    document.location.href = reinvio;
                }
            }
        });
    } else if (mode == "error") {
        if (testo == "") {
            testo = "Al momento non è possibile completare l'operazione";
        }
        swal({
            title: "Attenzione",
            text: testo,
            type: "error",
            showCancelButton: false,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "OK",
            closeOnConfirm: true,
            closeOnCancel: false
        });
    } else if (mode == "calmiere") {
        swal({
            title: "Calcolo Spesa Massima",
            text: "La spesa massima ammessa è di € " + testo,
            type: "success",
            showCancelButton: false,
            confirmButtonColor: "#31aa47",
            confirmButtonText: "OK",
            closeOnConfirm: true,
            closeOnCancel: false
        });
    }
}
function eliminaOperazione(id, nometabella, nomeColonna, paginaIndirizzamento) {
    swal({
        title: 'Attenzione',
        type: 'warning',
        text: "Sei sicuro di voler proseguire con l'operazione selezionata?",
        showCancelButton: true,
        confirmButtonColor: "#31aa47",
        confirmButtonText: "OK",
        cancelButtonText: "ANNULLA",
        closeOnConfirm: true,
        closeOnCancel: true
    }
    , function (isConfirm) {
        if (isConfirm) {
            var oggettopermesso = {
                id: id,
                nometabella: nometabella,
                nomeColonna: nomeColonna,
            };
            postdataClassic("../dbcall/" + paginaIndirizzamento, oggettopermesso, function (response) {
                console.log(response);
                var risp = jQuery.parseJSON(response);
                if (risp.esito === 1) {
                    location.reload();
                } else {
                    swalfunction("error", "");
                }
            });

        }
    });
}

function buttaFuori() {
    document.location.href = '../../operator.php';
}

function b64DecodeUnicode(str) {
    return atob(unescape(encodeURIComponent(str)));
}


function vedi(bid, btbl) {
    var oggetto = {
        id: bid,
        tbl: btbl
    };
    $.redirect("../vedi.php?mode=standard", oggetto);
}
function vediavanzato(bid, btbl, nomecolonna, idpartenza, id_impresa_mapping, tipoimpresa) {
    var oggetto = {
        id: bid,
        tbl: btbl,
        nomecolonna: nomecolonna,
        idpartenza: idpartenza,
        id_impresa_mapping: id_impresa_mapping,
        tipoimpresa: tipoimpresa
    };
    console.log(oggetto);
    $.redirect("../vedi.php?mode=avanzato", oggetto);
}


function toDate(selector) {
    var from = selector.split("-");
    return new Date(from[2], from[1] - 1, from[0]);
}


function isValidDate(dateString, tipo) {
    var dataattuale = new Date();
    var controllo = moment(dateString, 'DD-MM-YYYY', true).isValid();
    if (controllo === true) {
        var ricreo = dateString.split("-");
        var ricompongoladata = new Date(ricreo[1] + "-" + ricreo[0] + "-" + ricreo[2]);
        if (tipo == "minore") {
            if (ricompongoladata <= dataattuale) {
                return 1;
            } else {
                return 2;//significa che non è minore rispetto alla data odierna
            }
        } else if (tipo == "maggiore") {
            if (ricompongoladata >= dataattuale) {
                return 1;
            } else {
                return 3;//significa che non è maggiore rispetto alla data odierna
            }
        } else {
            return 1;
        }

    } else {
        return 0;
    }
}

function percentCalculation(a, b) {
    var c = (parseFloat(a) * parseFloat(b)) / 100;
    return parseFloat(c);
//    return parseFloat(c.toFixed(2));
}

function toFixedAme(number, decimals) {
    console.log("sto usando quest");
    var x = Math.pow(10, Number(decimals) + 1);
    return (Number(number) + (1 / x)).toFixed(decimals);
}

function getComuni() {
    var data = $.map(comuni, function (comune) {
        obj.id = obj.id || obj.pk; // replace pk with your identifier

        return obj;
    });
}

function getComuni(comuni, comune, regione) {
    var response = [];
    for (var i = 0; i < comuni.length; i++) {
        if (comuni[i].DESCRIZIONE.includes(comune) && (!regione || comuni[i].REGIONE == regione )) {
            response.push(comuni[i]);
        }
    }
    return response;
//    var data = $.map(comuni, function (comune, regione) {
//        if (){
//            response = data;
//        }
//        ùretì
//    });
}


//function firebaseError(errore) {
//    switch (errore) {
//        case 'auth/user-not-found':
//            errore = "Non esiste alcun utente corrispondente a questo identificatore. L'utente potrebbe essere stato eliminato.";
//            break;
//        case 'auth/user-disabled':
//            errore = "Utente non abilitato";
//            break;
//        case 'auth/wrong-password':
//            errore = "La password inserita non è valida";
//            break;
//        case 'auth/weak-password':
//            errore = "La password è troppo debole";
//            break;
//        case 'auth/email-already-in-use':
//            errore = "L'indirizzo email inserito è già presente.";
//            break;
//    }
//    functionSwall('error', errore, "");
//}
//function sendMailVerification(firebase) {
//    firebase.auth().currentUser.sendEmailVerification()
//            .then(function () {
//            })
//            .catch(function (error) {
//                // Error occurred. Inspect error.code.
//            });
//}
//function deleteUserFirebase(firebase) {
//    var user = firebase.auth().currentUser;
//
//    user.delete().then(function () {
//    }).catch(function (error) {
//        // An error happened.
//    });
//
//}
