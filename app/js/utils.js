$(document).ready(function () {
    $('#loader').hide();

});
function redirectDomanda(id_domanda) {
    var object = {
        domanda: id_domanda
    }
    $.redirect("domanda.php", object);
}

function isValidIBANNumber(input) {
    var CODE_LENGTHS = {
        AD: 24, AE: 23, AT: 20, AZ: 28, BA: 20, BE: 16, BG: 22, BH: 22, BR: 29,
        CH: 21, CR: 21, CY: 28, CZ: 24, DE: 22, DK: 18, DO: 28, EE: 20, ES: 24,
        FI: 18, FO: 18, FR: 27, GB: 22, GI: 23, GL: 18, GR: 27, GT: 28, HR: 21,
        HU: 28, IE: 22, IL: 23, IS: 26, IT: 27, JO: 30, KW: 30, KZ: 20, LB: 28,
        LI: 21, LT: 20, LU: 20, LV: 21, MC: 27, MD: 24, ME: 22, MK: 19, MR: 27,
        MT: 31, MU: 30, NL: 18, NO: 15, PK: 24, PL: 28, PS: 29, PT: 25, QA: 29,
        RO: 24, RS: 22, SA: 24, SE: 24, SI: 19, SK: 24, SM: 27, TN: 24, TR: 26
    };
    var iban = String(input).toUpperCase().replace(/[^A-Z0-9]/g, ""), // keep only alphanumeric characters
            code = iban.match(/^([A-Z]{2})(d{2})([A-Zd]+)$/), // match and capture (1) the country code, (2) the check digits, and (3) the rest
            digits;
// check syntax and length
    if (!code || iban.length !== CODE_LENGTHS[code[1]]) {
        return false;
    }
// rearrange country code and check digits, and convert chars to ints
    digits = (code[3] + code[1] + code[2]).replace(/[A-Z]/g, function (letter) {
        return letter.charCodeAt(0) - 55;
    });
// final check
    return mod97(digits);
}
function mod97(string) {
    var checksum = string.slice(0, 2), fragment;
    for (var offset = 2; offset < string.length; offset += 7) {
        fragment = String(checksum) + string.substring(offset, offset + 7);
        checksum = parseInt(fragment, 10) % 97;
    }
    return checksum;
}

/**/

function functionSwallMsg(mode, testo) {
    var scrivotesto = "";
    var indirizzo = "";
    if (testo != "") {
        scrivotesto = testo;
    }
    if (mode == "success") {
        Swal.fire({
            type: 'success',
            title: 'OK',
            text: scrivotesto,
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK',
            allowOutsideClick: false
        }).then((result) => {
        })
    } else if (mode == "error") {
        if (testo == "") {
            testo = "Al momento non è possibile completare l'operazione";
        }
        Swal.fire({
            type: 'error',
            title: 'Attenzione',
            text: testo,
            allowOutsideClick: false
        })

    }
}									  
function functionSwall(mode, testo, reinvio, valore) {
    var scrivotesto = "";
    var indirizzo = "";
    if (testo != "") {
        scrivotesto = testo;
    }
    if (reinvio && reinvio != "") {
        indirizzo = reinvio;
    } else {
        indirizzo = "";
    }
    if (mode == "success") {
        Swal.fire({
            type: 'success',
            title: 'OK',
            text: scrivotesto,
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK',
            allowOutsideClick: false
        }).then((result) => {
            if (result.value) {
                if (reinvio == "DOMANDA") {
                    var object = {
                        domanda: valore
                    };
                    $.redirect("domanda.php", object);
                } else if (reinvio == "ISTITUTO") {
                    var object = {
                        istituto: valore
                    };
                    if (valore > 0) {
                        $.redirect("istituto.php", object);
                    } else {
                        $.redirect("dashboard.php");
                    }
                } else {
                    if (valore != "") {
                        var object = {
                            azienda: valore
                        };
                        $.redirect(indirizzo + ".php", object);

                    } else {
                        document.location.href = indirizzo;

                    }

                }

            }
        });
    } else if (mode == "error") {
        if (testo == "") {
            testo = "Al momento non è possibile completare l'operazione";
        }
        Swal.fire({
            type: 'error',
            title: 'Attenzione',
            html: testo,
            allowOutsideClick: false
        });

    } else {
        if (testo == "") {
            testo = "Al momento non è possibile completare l'operazione";
        }
        Swal.fire({
            type: mode,
            title: 'Attenzione',
            html: testo,
            allowOutsideClick: false
        });
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
    $("#" + idform)[0].reset();
    console.log("fatto");
}
function checkViewConsent(url) {
    if (CONSENSO == 0) {
        functionSwall('error', "E' necessario registrare i propri dati e prestare il consenso al trattamento dei dati per usufruire dei servizi della piattaforma", "");
    } else {
        document.location.href = url;
    }

}
function saveConsenso() {
    $('#loader').show();
    var consenso = document.getElementById('consenso_informativo').checked;
    if (document.getElementById('consenso_informativo').checked) {
        var object = {
            consenso: consenso
        };
        postdataClassic(WS_CALL + "?module=account&action=saveConsenso", object, function (response) {
            $('#loader').hide();
            var risp = jQuery.parseJSON(response);
            if (risp.esito === 1) {
                functionSwall('success', 'Operazione Avvenuta con successo!!', BASE_HTTP + 'index.php');
            } else {
                functionSwall('error', risp.erroreDescrizione, "");
            }
        });
    } else {
        $('#loader').hide();
        functionSwall('error', "E' necessario registrare i propri dati e prestare il consenso al trattamento dei dati per usufruire dei servizi della piattaforma", "");
    }

}

function codiceFISCALE(codice_fiscale)
{
    var cf = codice_fiscale.toUpperCase();
    var cfReg = /^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/;
    if (!cfReg.test(cf))
        return false;
    var set1 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var set2 = "ABCDEFGHIJABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var setpari = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    var setdisp = "BAKPLCQDREVOSFTGUHMINJWZYX";
    var s = 0;
    for (i = 1; i <= 13; i += 2)
        s += setpari.indexOf(set2.charAt(set1.indexOf(cf.charAt(i))));
    for (i = 0; i <= 14; i += 2)
        s += setdisp.indexOf(set2.charAt(set1.indexOf(cf.charAt(i))));
    if (s % 26 != cf.charCodeAt(15) - 'A'.charCodeAt(0))
        return false;
    return true;
}



function number_format(number, decimals, decPoint, thousandsSep) { // eslint-disable-line camelcase  
    //   example 1: number_format(1234.56)
    //   returns 1: '1,235'
    //   example 2: number_format(1234.56, 2, ',', ' ')
    //   returns 2: '1 234,56'
    //   example 3: number_format(1234.5678, 2, '.', '')
    //   returns 3: '1234.57'
    //   example 4: number_format(67, 2, ',', '.')
    //   returns 4: '67,00'
    //   example 5: number_format(1000)
    //   returns 5: '1,000'
    //   example 6: number_format(67.311, 2)
    //   returns 6: '67.31'
    //   example 7: number_format(1000.55, 1)
    //   returns 7: '1,000.6'
    //   example 8: number_format(67000, 5, ',', '.')
    //   returns 8: '67.000,00000'
    //   example 9: number_format(0.9, 0)
    //   returns 9: '1'
    //  example 10: number_format('1.20', 2)
    //  returns 10: '1.20'
    //  example 11: number_format('1.20', 4)
    //  returns 11: '1.2000'
    //  example 12: number_format('1.2000', 3)
    //  returns 12: '1.200'
    //  example 13: number_format('1 000,50', 2, '.', ' ')
    //  returns 13: '100 050.00'
    //  example 14: number_format(1e-8, 8, '.', '')
    //  returns 14: '0.00000001'

    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number;
    var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
    var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep;
    var dec = (typeof decPoint === 'undefined') ? '.' : decPoint;
    var s = '';

    var toFixedFix = function (n, prec) {
        if (('' + n).indexOf('e') === -1) {
            return +(Math.round(n + 'e+' + prec) + 'e-' + prec);
        } else {
            var arr = ('' + n).split('e');
            var sig = '';
            if (+arr[1] + prec > 0) {
                sig = '+';
            }
            return (+(Math.round(+arr[0] + 'e' + sig + (+arr[1] + prec)) + 'e-' + prec)).toFixed(prec);
        }
    };

    // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec).toString() : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }

    return s.join(dec);
}

function dataTableEventXhr() {
    $('#' + $('.dataTable').attr('id')).on('xhr.dt', function (e, settings, json, xhr) {
        if (json.esito <= 0) {
            Swal.fire({
                title: "Attenzione",
                text: json.erroreDescrizione,
                icon: "warning",
                closeModal: false,
                showCancelButton: false,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'OK'
            }).then((result) => {
//                        if (result.value) {
                $.redirect(BASE_HTTP + "login.php");
//                        }
            });
        }
    });
}


function goToMagazzino(url, azienda, tab) {
    var object = {
        azienda: azienda,
        tab: tab
    };
    $.redirect(url + ".php", object);
}
            
function goToDashboard(url) {                
    $.redirect(HTTP_PRIVATE_SECTION+url + ".php");
}

function goToMovimento(url, azienda, tipo_mov , id_mov) {
    var object = {
        azienda: azienda,
        tipo_movimento: tipo_mov,
        id_movimento: id_mov
    };
    $.redirect(url + ".php", object);
}
/**
 * Richiama una pagina in post passandogli il cuaa dell'azienda
 * @param {type} url
 * @param {type} prg_scheda
 * @returns {undefined}
 */
function goToAzienda(url, azienda) {
    var object = {
        azienda: azienda
    };
    $.redirect(HTTP_PRIVATE_SECTION+url + ".php", object);
}

function goToPage(url, id) {
    var object = {
        id: id
    };
    $.redirect(HTTP_PRIVATE_SECTION+url + ".php", object);
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

realAssetsFrameworkClass = function () {
},
        realAssetsFrameworkClass.prototype =
        {
            /*
             * Modal Loader Show/Hide
             */
            ShowLoader: function () {
                $("#wait-spinner").show();
            },
            HideLoader: function () {
                $("#wait-spinner").hide();
            },

            /**
             * Apre un MessageBox con icona di esclamazione
             */
            MessageWarning: function (text, title, width) {
                if (!title)
                    title = "Validazione modulo";
                if (!width)
                    width = 300;

                Swal.fire({
                    title: title,
                    text: text,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonText: "OK"
                });
            },

            /**
             * Apre un MessageBox con icona di esclamazione
             */
            MessageExclamation: function (text, title, width) {
                if (!title)
                    title = "Validazione modulo";
                if (!width)
                    width = 300;

                Swal.fire({
                    title: title,
                    text: text,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonText: "OK"
                });
            },
            MessageSuccess: function (text, title, width, noReload) {
                if (!title)
                    title = "Validazione modulo";
                if (!width)
                    width = 300;
                if (text == "") {
                    text = "OK";
                }

                Swal.fire({
                    title: title,
                    text: text,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonText: "OK"
                }).then(result => {
                    if (noReload != true) {
                        location.reload();
                    }
                });

            },
            /**
             * Apre un MessageBox con icona di errore
             */
            MessageError: function (text, title, width) {
                if (!title)
                    title = "Errore imprevisto";
                if (!width)
                    width = 300;
                if (text == "") {
                    text = "Al momento non è possibile completare l'operazione";
                }
                Swal.fire({
                    title: title,
                    text: text,
                    type: "error",
                    showCancelButton: false,
                    confirmButtonText: "OK"
                });



            },

            /**
             * Apre un MessageBox con icona di informazioni
             */
            MessageInfo: function (text, title, width) {
                if (!title)
                    title = "Errore imprevisto";
                if (!width)
                    width = 300;
                if (text == "") {
                    text = "Al momento non è possibile completare l'operazione";
                }
                Swal.fire({
                    title: title,
                    text: text,
                    icon: "info",
                    showCancelButton: false,
                    confirmButtonText: "OK"
                });
            },

            ModModal: function (id_form, module, urlCallBack = '') {
                $("#loader").show();
                var form = document.getElementById(id_form);
                var formData = new FormData(form);
                $.ajax({
                    url: WS_CALL + '?module=' + module,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function (data) {
                    },
                    success: function (response) {
                        //console.log(response);
                        var res = jQuery.parseJSON(response);
                        var risposta = res.esito;
                        var descrizione_errore = res.descrizioneErrore;
                        $("#loader").hide();
                        if (risposta === 1) {
                            if (urlCallBack != "") {
                                //document.location.href = urlCallBack;
                                urlCallBack(res);
                                return;
                            } else {
                                location.reload();
                                return;
                            }
                        } else {
                            realAssetsFramework.MessageError(descrizione_errore, 'Attenzione');
                            return;
                        }
                    },
                    error: function (xhr, status, error) {
                        $('#loader').hide();
                        alert("An AJAX error occured: " + status + "\nError: " + error);
                        console.log(xhr);
                    }
                });

            },
            takeChargeView: function (id, module, action, dataTable = '', id_form = '') {
                var id = id;
                if (id_form != "")
                    $("#" + id_form)[0].reset();

                $.post(WS_CALL + '?module=' + module + '&action=' + action,
                        {id: id},
                        function (data) {

                            if (dataTable != "") {
                                var datatbale_name = $(dataTable).DataTable();
                                datatbale_name.ajax.reload(null, false);
                            }

                            $.each(JSON.parse(data), function (i, item) {
                                $('#view_' + i).empty();
                                $('#view_' + i).append("<span>" + item + "</span>");
                            });
                        });
            },
            takeCharge: function (id, module, action, dataTable = '', id_form = '') {
                var id = id;
                if (id_form != "")
                    $("#" + id_form)[0].reset();

                $.post(WS_CALL + '?module=' + module + '&action=' + action,
                        {id: id},
                        function (data) {
                            if (dataTable != "") {
                                var datatbale_name = $(dataTable).DataTable();
                                datatbale_name.ajax.reload(null, false);
                            }
                            //console.log(JSON.parse(data));
                            $.each(JSON.parse(data), function (i, item) {
                                $('#edit_' + i).val(item);
                                //console.log('#edit_' + i + ' --> '+item);                                
                            });
                            if (module == 'faq_utenti' && action == 'load') {
                                if ($('#edit_STATO').val() > 0) {
                                    $(".btn-presavisione").html("Da visionare");
                                    //$(".btn-presavisione").prop("hidden", true);
                                } else {
                                    $(".btn-presavisione").html("Presa visione");
                                    //$(".btn-presavisione").prop("hidden", false);
                                }
                            }else if(module == 'persone' && action == 'load'){                                
                                if ($('#edit_ISTAT_NASCITA').val() > 0) {
                                    $("#edit_COMUNE_NASCITA").select2("trigger", "select", {
                                        data: {
                                            id: $('#edit_ISTAT_NASCITA').val(),
                                            text: $('#edit_TXT_COMUNE_NASCITA').val(),
                                            cap: '',
                                            codice_provincia: '',
                                            codice_istat: $('#edit_ISTAT_NASCITA').val()
                                        }
                                    })
                                }
                            }
                        });
            },
            takeChargeConfirm: function (id, module, action, dataTable = '', id_form = '') {
                var id = id;
                if (id_form != "")
                    $("#" + id_form)[0].reset();
                Swal.fire({
                    title: "Attenzione",
                    text: "Sei sicuro di volere procedere con l'operazione?",
                    icon: "warning",
                    closeModal: true,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.value) {
                        $.post(WS_CALL + '?module=' + module + '&action=' + action,
                                {id: id},
                                function (data) {
                                    if (dataTable != "") {
                                        var datatbale_name = $(dataTable).DataTable();
                                        datatbale_name.ajax.reload(null, false);
                                    }

                                    $.each(JSON.parse(data), function (i, item) {
                                        $('#edit_' + i).val(item);
                                        //console.log('#edit_' + i + ' --> ' + item);
                                        /*if ($('#edit_' + i).is('select')) {
                                         $('#edit_' + i).formSelect();
                                         }*/
                                    });
                                });
                    }
                });
            },
            takeChargeCodiciVari: function (id, gruppo, module, action, dataTable = '', id_form = '') {

                var id_codice = id;
                var gruppo = gruppo;
                if (id_form != ""){
                    $("#" + id_form)[0].reset();
                }
                var go = false;
                
                if(action === 'delete'){
                    Swal.fire({
                        title: "Cancellazione",
                        text: "Sei sicuro di cancellare il record selezionato?",
                        //icon: "info",
                        //closeModal: true,
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        
                        if (result.value) {
                            $.post(WS_CALL + '?module=' + module + '&action=' + action,
                                {ID_CODICE: id_codice, GRUPPO: gruppo},
                                function (data) {
                                    console.log(data);
                                    if (dataTable != "") {
                                        var datatbale_name = $(dataTable).DataTable();
                                        datatbale_name.ajax.reload(null, false);
                                    }

                                    $.each(JSON.parse(data), function (i, item) {
                                        $('#edit_' + i).val(item);
                                        if ($('#edit_' + i).is('select')) {
                                            $('#edit_' + i).formSelect();
                                        }
                                    });
                                });
                        }
                    });
                }else{
                    go = true;
                }
                console.log('entro');
                if(go === true){
                 
                    $.post(WS_CALL + '?module=' + module + '&action=' + action,
                            {ID_CODICE: id_codice, GRUPPO: gruppo},
                            function (data) {
                                console.log(data);
                                if (dataTable != "") {
                                    var datatbale_name = $(dataTable).DataTable();
                                    datatbale_name.ajax.reload(null, false);
                                }

                                $.each(JSON.parse(data), function (i, item) {
                                    $('#edit_' + i).val(item);
                                    if ($('#edit_' + i).is('select')) {
                                        $('#edit_' + i).formSelect();
                                    }
                                });
                            });
                }
            },
            takeDelete: function (id, module, action, dataTable = '', reload = '') {
                var id = id;

                Swal.fire({
                    title: "Cancellazione",
                    text: "Sei sicuro di cancellare il record selezionato?",
                    icon: "warning",
                    closeModal: true,
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: WS_CALL + '?module=' + module + '&action=' + action,
                            type: "POST",
                            data: {id: id},
                            dataType: "html",
                            success: function () {
                                if (dataTable != "") {
                                    var datatbale_name = $(dataTable).DataTable();
                                    datatbale_name.ajax.reload(null, false);
                                }
                                var NoReolad = true;
                                if (reload) {
                                    document.location.href = reload;

                                }
                                realAssetsFrameworkClass.prototype.MessageSuccess("Il record e' stato cancellato con succcesso", "Cancellazione effettuata", '', NoReolad);
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                                realAssetsFrameworkClass.prototype.MessageSuccess();
                            }
                        });
                    }

//                    $.post(WS_CALL + '?module=' + module + '&action=' + action,
//                            {id: id},
//                            function (data) {
//                                
//                                
//
//                            });
                });

            },
            CheckStatusSession: function (json) {
                if (json.esito <= 0) {
                    Swal.fire({
                        title: "Attenzione",
                        text: json.erroreDescrizione,
                        icon: "warning",
                        closeModal: false,
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'OK'
                    }).then((result) => {
//                        if (result.value) {
                            $.redirect(BASE_HTTP + "login.php");
//                        }
                    });
                }
            },
            DataTableEventXhr: function(){
                $('#' + $('.dataTable').attr('id')).on( 'xhr.dt', function(e, settings, json, xhr){
                    realAssetsFramework.CheckStatusSession(json);
                });
            },
            ButtonAdd: function (divId, config, fn) {
                var div = document.getElementById(divId);
                if (div !== null) {
                    var btn = document.createElement('button');
                    var text = (config && config.text ? config.text : "Nuovo");
                    var href = (config && config.href ? config.href : "");
                    var txt = document.createTextNode(text);
                    //var hidden = (permission !== '' ? (permission.Update ? false : true) : (config && config.hidden ? config.hidden : false));
                    var classDisabled = "";
//                    if (hidden)
//                        classDisabled = "disabled";
                    btn.appendChild(txt);
                    btn.setAttribute('type', 'button');
                    btn.setAttribute('class', 'btn btn-primary ' + classDisabled + '');
                    btn.setAttribute('data-toggle', 'modal');
                    btn.setAttribute('data-target', classDisabled);

                    if (href != "")
                        btn.setAttribute('href', href);
                    if (fn)
                        btn.setAttribute('onclick', fn);
                    btn.setAttribute('id', 'buttonAdd');
                    div.appendChild(btn);
                }
            },

            MessageConfirmDelete(text, title, width, url, data, fnCallback) {
                if (!title)
                    title = "Conferma cancellazione";
                if (!width)
                    width = 300;
                if (text == "") {
                    text = "Vuoi procedere con la cancellazione del record?";
                }
                swal({
                    title: title,
                    text: text,
                    icon: "warning",
                    closeModal: true,
                    dangerMode: true,
                    onClose: () => {
                        fnCallback();
                    },
                    buttons: {cancel: {
                            text: "ANNULLA",
                            value: false,
                            visible: true,
                            className: "",
                            closeModal: true
                        }, confirm: {
                            text: "OK",
                            value: true,
                            visible: true,
                            className: "",
                            closeModal: false
                        }}
                }).then(function (isConfirm) {
                    if (!isConfirm)
                        return;
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: data,
                        dataType: "html",
                        success: function () {
//                        if (callback){
//                            callback();
//                        }
                            realAssetsFrameworkClass.prototype.MessageSuccess("Il record e' stato cancellato con successo", "Cancellazione effettuata");
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            realAssetsFrameworkClass.prototype.MessageSuccess();
                        }
                    });
                });
            },

            /**
             * Apre un MessageBox che richiama una funziona di callback
             */
            MessageConfirmGeneric: function (text, title, width, url, data, fnCallbackSuccess, fnCallbackFailure, retid) {
                if (!title)
                    title = "Errore imprevisto";
                if (!width)
                    width = 300;
                if (text == "") {
                    text = "Al momento non è possibile completare l'operazione";
                }
                swal({
                    title: title,
                    text: text,
                    icon: "warning",
                    closeModal: true,
                    buttons: {cancel: {
                            text: "ANNULLA",
                            value: false,
                            visible: true,
                            className: "",
                            closeModal: true
                        }, confirm: {
                            text: "OK",
                            value: true,
                            visible: true,
                            className: "",
                            closeModal: false
                        }}
                }).then(function (isConfirm) {
                    if (!isConfirm)
                        return;
                    $.ajax({
                        url: url,
                        type: "POST",
                        data: data,
                        dataType: "html",
                        success: function (result) {
                            console.log(result);
                            var res = eval(result);
                            if (retid != null && retid > 0) {
                                return res[0].id;

                            } else {
                                if (res[0].esito == 1) {
                                    realAssetsFrameworkClass.prototype.MessageSuccess("Operazione completata con succcesso", "Operazione effettuata");
                                    if (fnCallbackSuccess) {
                                        fnCallbackSuccess();
                                    }
                                } else {
                                    realAssetsFrameworkClass.prototype.MessageError();
                                }
                            }
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            realAssetsFrameworkClass.prototype.MessageError();
                            if (fnCallbackFailure) {
                                fnCallbackFailure();
                            }
                        }
                    });
                });
            },
            MessageConfirmFunction: function (text, title, width, fnCallbackSuccess) {
                if (!title)
                    title = "Errore imprevisto";
                if (!width)
                    width = 300;
                if (text == "") {
                    text = "Al momento non è possibile completare l'operazione";
                }
                swal({
                    title: title,
                    text: text,
                    icon: "warning",
                    closeModal: true,
                    buttons: {cancel: {
                            text: "ANNULLA",
                            value: false,
                            visible: true,
                            className: "",
                            closeModal: true
                        }, confirm: {
                            text: "OK",
                            value: true,
                            visible: true,
                            className: "",
                            closeModal: false
                        }}
                }).then(function (isConfirm) {
                    if (!isConfirm)
                        return;
                    if (fnCallbackSuccess) {
                        fnCallbackSuccess();
                    }
                });
            },
            /**
             * Download forzato di un file
             * @param {type} url
						   
             * @returns {undefined}
             */
            downloadURL: function (url) {
                var hiddenIFrameID = 'hiddenDownloader',
                        iframe = document.getElementById(hiddenIFrameID);
                if (iframe === null) {
                    iframe = document.createElement('iframe');
                    iframe.id = hiddenIFrameID;
                    iframe.style.display = 'none';
                    document.body.appendChild(iframe);
                }
                iframe.src = url;
            },

            /*
             * restituisce le ore
             */
            GetDay: function (min) {
                return Math.floor(min / (60 * 24));
            },

            /*
             * restituisce le ore
             */
            GetHour: function (min) {
                var rest = min % (60 * 24);
                return Math.floor(rest / 60);
            },

            /*
             * restituisce i minuti
             */
            GetMinute: function (min) {
                return Math.floor(min % 60);
            },

            /*
             * aggiunge zeri di riempimento prima del numero
             */
            Pad: function (number, length) {
                var str = '' + number;
                while (str.length < length)
                {
                    str = '0' + str;
                }
                return str;
            },

            /*
             * clona un array
             */
            ArrayClone: function (_ArryToClone) {
                var _Clone = new Array();
                for (_IdClone in _ArryToClone)
                    if (_ArryToClone[_IdClone].Constructor == Array)
                        Clone(_ArryToClone[_IdClone])
                    else
                        _Clone[_IdClone] = _ArryToClone[_IdClone]
                return _Clone
            },

            /**
             ****** IN ARRAY AS PHP ******
             **/
            In_array: function (needle, haystack, argStrict) {
                var key = '', strict = !!argStrict;
                if (strict) {
                    for (key in haystack) {
                        if (haystack[key] === needle) {
                            return true;
                        }
                    }
                } else {
                    for (key in haystack) {
                        if (haystack[key] == needle) {
                            return true;
                        }
                    }
                }
                return false;
            },

            /*
             ***** From seconds to locateDate ******
             **/
            SecondsToLocaleDate: function (d, hideMs) {
                // if (isNaN(d))
                // {
                // return false;
                // }
                hideMs = (hideMs == true ? true : false);
                var newDate = new Date( );
                var ms = '';
                if (cnxplayer && d.toString().indexOf('.') > 0)
                {
                    dp = d.toString().split('.');
                    ms = '.' + dp[1];
                    if (ms.length == 2)
                        ms += '00';
                    else if (ms.length == 3)
                        ms += '0';
                    ms = ms.substring(0, 4);
                }
                newDate.setTime(d * 1000);
                dateString = newDate.toLocaleString();
                ds = "";
                if (dateString.indexOf('/') > 0)
                {
                    // dateString = dateString.replace("/", " ");
                    ds = dateString.split('/');
                    dateString = ds[0] + ' ' + ds[1] + ' ' + ds[2];//+' '+ds[3];
                } else
                {
                    ds = dateString.split(' ');
                    dateString = ds[1] + ' ' + ds[2].substring(0, 3) + ' ' + ds[3] + ' ' + ds[4];
                }
                return (hideMs ? dateString : dateString + ms);
            },

            /*
             ***** From seconds to time format H:m:s ******
             **/
            SecondsToHms: function (d) {
                d = Number(d);
                var h = Math.floor(d / 3600);
                var m = Math.floor(d % 3600 / 60);
                var s = Math.floor(d % 3600 % 60);
                return ((h < 10 ? "0" + h : h) + ":" + (m >= 0 ? (h >= 0 && m < 10 ? "0" : "") + m + ":" : "0:") + (s < 10 ? "0" : "") + s);
            },

            /*
             ******* PRINT_R AS PHP*******
             */
            Print_r: function (array, return_val) {
                var output = "", pad_char = " ", pad_val = 4;

                var formatArray = function (obj, cur_depth, pad_val, pad_char) {
                    if (cur_depth > 0) {
                        cur_depth++;
                    }

                    var base_pad = repeat_char(pad_val * cur_depth, pad_char);
                    var thick_pad = repeat_char(pad_val * (cur_depth + 1), pad_char);
                    var str = "";

                    if (obj instanceof Array || obj instanceof Object) {
                        str += "Array\n" + base_pad + "(\n";
                        for (var key in obj) {
                            if (obj[key] instanceof Array) {
                                str += thick_pad + "[" + key + "] => " + formatArray(obj[key], cur_depth + 1, pad_val, pad_char);
                            } else {
                                str += thick_pad + "[" + key + "] => " + obj[key] + "\n";
                            }
                        }
                        str += base_pad + ")\n";
                    } else if (obj == null || obj == undefined) {
                        str = '';
                    } else {
                        str = obj.toString();
                    }

                    return str;
                };

                var repeat_char = function (len, pad_char) {
                    var str = "";
                    for (var i = 0; i < len; i++) {
                        str += pad_char;
                    }
                    ;
                    return str;
                };
                output = formatArray(array, 0, pad_val, pad_char);

                if (return_val !== true) {
                    document.write("<pre>" + output + "</pre>");
                    return true;
                } else {
                    return output;
                }
            },

            /*
             *********** str_replace ************
             */
            str_replace: function (search, replace, subject, count) {
                // %          note 1: The count parameter must be passed as a string in order
                // %          note 1:  to find a global variable in which the result will be given
                // *     example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
                // *     returns 1: 'Kevin.van.Zonneveld'
                // *     example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars');
                // *     returns 2: 'hemmo, mars'    var i = 0,
                j = 0,
                        temp = '',
                        repl = '',
                        sl = 0, fl = 0,
                        f = [].concat(search),
                        r = [].concat(replace),
                        s = subject,
                        ra = Object.prototype.toString.call(r) === '[object Array]', sa = Object.prototype.toString.call(s) === '[object Array]';
                s = [].concat(s);
                if (count) {
                    this.window[count] = 0;
                }
                for (i = 0, sl = s.length; i < sl; i++) {
                    if (s[i] === '') {
                        continue;
                    }
                    for (j = 0, fl = f.length; j < fl; j++) {
                        temp = s[i] + '';
                        repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
                        s[i] = (temp).split(f[j]).join(repl);
                        if (count && s[i] !== temp) {
                            this.window[count] += (temp.length - s[i].length) / f[j].length;
                        }
                    }
                }
                return sa ? s : s[0];
            },

            /*
             *********** str_replace ************
             */
            array2json: function (arr) {
                var parts = [];
                var is_list = (Object.prototype.toString.apply(arr) === '[object Array]');

                for (var key in arr) {
                    var value = arr[key];
                    if (typeof value == "object") { //Custom handling for arrays
                        if (is_list)
                            parts.push(array2json(value)); /* :RECURSION: */
                        else
                            parts[key] = array2json(value); /* :RECURSION: */
                    } else {
                        var str = "";
                        if (!is_list)
                            str = '"' + key + '":';

                        //Custom handling for multiple data types
                        if (typeof value == "number")
                            str += value; //Numbers
                        else if (value === false)
                            str += 'false'; //The booleans
                        else if (value === true)
                            str += 'true';
                        else
                            str += '"' + value + '"'; //All other things
                        // :TODO: Is there any more datatype we should be in the lookout for? (Functions?)

                        parts.push(str);
                    }
                }
                var json = parts.join(",");

                if (is_list)
                    return '[' + json + ']';//Return numerical JSON
                return '{' + json + '}';//Return associative JSON
            },

            /*
             *********** json_encode ************
             */
            Json_encode: function (mixed_val) {
                // http://kevin.vanzonneveld.net
                // +      original by: Public Domain (http://www.json.org/json2.js)
                // + reimplemented by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
                // +      improved by: Michael White
                // +      input by: felix
                // +      bugfixed by: Brett Zamir (http://brett-zamir.me)
                // *        example 1: json_encode(['e', {pluribus: 'unum'}]);
                // *        returns 1: '[\n    "e",\n    {\n    "pluribus": "unum"\n}\n]'
                /*
                 http://www.JSON.org/json2.js
                 2008-11-19
                 Public Domain.
                 NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.
                 See http://www.JSON.org/js.html
                 */
                var retVal, json = this.window.JSON;
                try {
                    if (typeof json === 'object' && typeof json.stringify === 'function') {
                        retVal = json.stringify(mixed_val); // Errors will not be caught here if our own equivalent to resource
                        //  (an instance of PHPJS_Resource) is used
                        if (retVal === undefined) {
                            throw new SyntaxError('json_encode');
                        }
                        return retVal;
                    }

                    var value = mixed_val;

                    var quote = function (string) {
                        var escapable = /[\\\"\u0000-\u001f\u007f-\u009f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g;
                        var meta = {// table of character substitutions
                            '\b': '\\b',
                            '\t': '\\t',
                            '\n': '\\n',
                            '\f': '\\f',
                            '\r': '\\r',
                            '"': '\\"',
                            '\\': '\\\\'
                        };

                        escapable.lastIndex = 0;
                        return escapable.test(string) ? '"' + string.replace(escapable, function (a) {
                            var c = meta[a];
                            return typeof c === 'string' ? c : '\\u' + ('0000' + a.charCodeAt(0).toString(16)).slice(-4);
                        }) + '"' : '"' + string + '"';
                    };

                    var str = function (key, holder) {
                        var gap = '';
                        var indent = '    ';
                        var i = 0; // The loop counter.
                        var k = ''; // The member key.
                        var v = ''; // The member value.
                        var length = 0;
                        var mind = gap;
                        var partial = [];
                        var value = holder[key];

                        // If the value has a toJSON method, call it to obtain a replacement value.
                        if (value && typeof value === 'object' && typeof value.toJSON === 'function') {
                            value = value.toJSON(key);
                        }

                        // What happens next depends on the value's type.
                        switch (typeof value) {
                            case 'string':
                                return quote(value);

                            case 'number':
                                // JSON numbers must be finite. Encode non-finite numbers as null.
                                return isFinite(value) ? String(value) : 'null';

                            case 'boolean':
                            case 'null':
                                // If the value is a boolean or null, convert it to a string. Note:
                                // typeof null does not produce 'null'. The case is included here in
                                // the remote chance that this gets fixed someday.				
                                return String(value);

                            case 'object':
                                // If the type is 'object', we might be dealing with an object or an array or
                                // null.
                                // Due to a specification blunder in ECMAScript, typeof null is 'object',
                                // so watch out for that case.
                                if (!value) {
                                    return 'null';
                                }
                                if ((this.PHPJS_Resource && value instanceof this.PHPJS_Resource) || (window.PHPJS_Resource && value instanceof window.PHPJS_Resource)) {
                                    throw new SyntaxError('json_encode');
                                }

                                // Make an array to hold the partial results of stringifying this object value.
                                gap += indent;
                                partial = [];

                                // Is the value an array?
                                if (Object.prototype.toString.apply(value) === '[object Array]') {
                                    // The value is an array. Stringify every element. Use null as a placeholder
                                    // for non-JSON values.
                                    length = value.length;
                                    for (i = 0; i < length; i += 1) {
                                        partial[i] = str(i, value) || 'null';
                                    }

                                    // Join all of the elements together, separated with commas, and wrap them in
                                    // brackets.
                                    v = partial.length === 0 ? '[]' : gap ? '[\n' + gap + partial.join(',\n' + gap) + '\n' + mind + ']' : '[' + partial.join(',') + ']';
                                    gap = mind;
                                    return v;
                                }

                                // Iterate through all of the keys in the object.
                                for (k in value) {
                                    if (Object.hasOwnProperty.call(value, k)) {
                                        v = str(k, value);
                                        if (v) {
                                            partial.push(quote(k) + (gap ? ': ' : ':') + v);
                                        }
                                    }
                                }

                                // Join all of the member texts together, separated with commas,
                                // and wrap them in braces.
                                v = partial.length === 0 ? '{}' : gap ? '{\n' + gap + partial.join(',\n' + gap) + '\n' + mind + '}' : '{' + partial.join(',') + '}';
                                gap = mind;
                                return v;
                            case 'undefined':
                            // Fall-through
                            case 'function':
                            // Fall-through
                            default:
                                throw new SyntaxError('json_encode');
                        }
                    };

                    // Make a fake root object containing our value under the key of ''.
                    // Return the result of stringifying the value.
                    return str('', {
                        '': value
                    });

                } catch (err) { // Todo: ensure error handling above throws a SyntaxError in all cases where it could
                    // (i.e., when the JSON global is not available and there is an error)
                    if (!(err instanceof SyntaxError)) {
                        throw new Error('Unexpected error type in json_encode()');
                    }
                    this.php_js = this.php_js || {};
                    this.php_js.last_error_json = 4; // usable by json_last_error()
                    return null;
                }
            },

            /*
             * Check if exists key in array
             */
            Array_key_exists: function (key, search) {
                // Checks if the given key or index exists in the array  
                // 
                // version: 1109.2015
                // discuss at: http://phpjs.org/functions/array_key_exists    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
                // +   improved by: Felix Geisendoerfer (http://www.debuggable.com/felix)
                // *     example 1: array_key_exists('kevin', {'kevin': 'van Zonneveld'});
                // *     returns 1: true
                if (!search || (search.constructor !== Array && search.constructor !== Object))
                {
                    return false;
                }
                return key in search;
            },

            /*
             * Trim function (global, left trim and right trim)
             */
            Trim: function (stringa) {
                return stringa.replace(/^\s+|\s+$/g, "");
            },
            Ltrim: function (stringa) {
                return stringa.replace(/^\s+/, "");
            },
            Rtrim: function (stringa) {
                return stringa.replace(/\s+$/, "");
            },
            
            
            GetPosizioneSuccessiva: function (posizione_economica = null){
                var posizione_economica_successiva = '';
                if(posizione_economica!=null){
                    var cat = posizione_economica.split('',2);            
                    var lettera = cat[0];
                    var numero = parseInt(cat[1]);
                    console.log('POS_ECONOMICA');
                    console.log(POS_ECONOMICA);
                    console.log('LETTERA');
                    console.log(lettera);    
                    console.log('NUMERO');
                    console.log(numero);
                    console.log(POS_ECONOMICA[lettera][numero]);
                    console.log(POS_ECONOMICA[lettera]);
                    console.log(POS_ECONOMICA[lettera]['MAX_POSIZIONE']);

                    var posizione_economica_successiva = (numero+1>= POS_ECONOMICA[lettera]['MAX_POSIZIONE'] ? POS_ECONOMICA[lettera][POS_ECONOMICA[lettera]['MAX_POSIZIONE']]  : POS_ECONOMICA[lettera][numero+1] );
                    console.log('POS_ECONOMICA_SUCCESSIVA');
                    console.log(posizione_economica_successiva);

                }

                return posizione_economica_successiva;

            },
            
            ViewAllParticelleMaps: function (divId = 'mapsModal', scheda, ragsoc) {

                $('#loader').show();

                var scheda = atob(scheda);
                var ragsoc = atob(ragsoc);
                //console.log(feauters);
                $('#mapTitle').empty();// Svuoto il titolo
                $('#mapsid').empty();// Svuoto il div della maps
                var divMaps = document.createElement('div');
                divMaps.setAttribute('id', 'mapid-' + scheda);
                divMaps.setAttribute('class', 'mapid');
                $('#mapsid').append('<div class="modal-header"><h5 id="mapTitle" class=" white-text"></h5></div>');
                $('#mapsid').append(divMaps);
                //$('#mapid').empty();         

                $.ajax({
                    type: "POST",
                    url: WS_CALL,
                    data: "module=isola&action=geojson&prg_scheda=" + scheda,
                    dataType: "text",
                    success: function (msg)
                    {

                        $('#loader').hide();

                        //console.log(msg);
                        var popup = L.popup();
                        var gpsDecode = JSON.parse(msg);
                        var geoDecode = JSON.parse(gpsDecode[0].geomjson);

                        $('#mapTitle').append(ragsoc);
                        var lat = geoDecode.features[0].geometry.coordinates[0][0][0][1];
                        var lng = geoDecode.features[0].geometry.coordinates[0][0][0][0];
                        //console.log(geoDecode.features[0].geometry.coordinates[0][0][0]);
                        //L.map('mapid').stop();
                        var mymap = L.map('mapid-' + scheda);
                        //14.4349094384902, 36.9140974123678
                        var latlng = L.latLng(lat, lng);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 18,
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(mymap);

                        mymap.setView(latlng, 16);
                        console.log(geoDecode);
                        L.geoJSON(geoDecode, {
                            style: function (feature) {
                                return {
                                    stroke: true,
                                    color: '#000000',
                                    weight: 3,
                                    fill: true,
                                    fillColor: '#ff5200',
                                    fillOpacity: 1
                                };
                            }, onEachFeature: function (feature, layer) {
                                var info = "";

                                if (feature.properties.codice_isola) {
                                    var codice = feature.properties.codice_isola;
                                    var cuua = codice.split('/');

                                    if (cuua[1]) {
                                        info += '<b>Cuua</b>: ' + cuua[1] + '</br>';
                                    }

                                    info += '<b>Isola</b>: ' + feature.properties.codice_isola + '</br>';
                                }
                                if (feature.properties.superficie) {
                                    info += '<b>Superfice</b>: ' + feature.properties.superficie + ' mq</br>';
                                }
                                if (feature.properties.foglio) {
                                    info += '<b>Foglio</b>: ' + feature.properties.foglio + ' <b>Sez.</b>:' + feature.properties.sezione;
                                }
                                layer.bindPopup(info);
                            }
                        }).addTo(mymap);

                        document.getElementById(divId).style.display = 'block';
                        setTimeout(function () {
                            mymap.invalidateSize();
                            mymap.on('click', function (e) {
                                popup
                                        .setLatLng(e.latlng)
                                        .setContent("Hai fatto clic sulla mappa in " + e.latlng.toString())
                                        .openOn(mymap);
                            });
                        }, 100);
                    },
                    error: function ()
                    {
                        alert("Chiamata fallita, si prega di riprovare...");
                    }
                });

            }

        }
        
        

// Inizializzazione Fitosan Framework
var realAssetsFramework = new realAssetsFrameworkClass();