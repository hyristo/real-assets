$(document).ready(function () {
    $(".difesa").hide();
    $(".nutrizione").hide();
    $(".irrigazione").hide();
    $(".operazione").hide();
    $(".raccolta").hide();
    $(".visualizzaFitApp").hide();

});

$('#ProdottiFertilizzanti').on('select2:select', function (e) {
    var data = e.params.data;
    if (data) {
        $("#ID_PRODOTTO_FITOSANITARIO").val(data.id);
        $('#unita_misura_sigla').val(data.unita_misura_a);
        $('#unita_misura').val(data.UNITA_MISURA);
        $('#giacenze').val(data.giacenze);
    } else {
        functionSwall('error', "Selezionare un articolo presente nella lista sottostante!", 'error');
        $("#ID_PRODOTTO_FITOSANITARIO").val("");
        $('#unita_misura_sigla').val("");
        $('#unita_misura').val("");

        $('#giacenze').val("");
    }
});

$('#ProdottiFitosanitari').on('select2:select', function (e) {
    var data = e.params.data;
    if (data) {
        $("#ID_PRODOTTO_FITOSANITARIO").val(data.id);
        $('#unita_misura_sigla').val(data.unita_misura_a);
        $('#unita_misura').val(data.UNITA_MISURA);
        $('#giacenze').val(data.giacenze);
    } else {
        functionSwall('error', "Selezionare un articolo presente nella lista sottostante!", 'error');
        $("#ID_PRODOTTO_FITOSANITARIO").val("");
        $('#unita_misura_sigla').val("");
        $('#unita_misura').val("");
        $('#giacenze').val("");
    }
});

function saveMovimento() {
    $('#loader').show();
    var form = document.getElementById('add-form-tracciamento');
    var formInvio = new FormData(form);
    formInvio.append('module', 'registro_tracciamenti');
    formInvio.append('action', 'save');
    postdata(WS_CALL, formInvio, function (response) {
        console.log(response);
        $('#loader').hide();
        var risp = jQuery.parseJSON(response);
        if (risp.esito === 1) {
            functionSwall('success', 'Operazione Effettuata con successo', HTTP_PRIVATE_SECTION + 'registro_tracciamenti', risp.cuaa);
        } else {
            functionSwall('error', risp.erroreDescrizione, '');
        }
    });
}



function sceltaSpecie(ricevuto, prg_scheda) {
    $(".visualizzaFitApp").show();
    var object = {
        'module': 'registro_tracciamenti',
        'action': 'listSelect',
        'specie': ricevuto.value,
        'prg_scheda': prg_scheda

    };
    $('#loader').show();
    postdataClassic(WS_CALL, object, function (response) {
        var risp = jQuery.parseJSON(response);
        var descrizione = "";
        $('#container').empty();
        var mymap = L.map('mapid-' + prg_scheda);
        var geoDecode = JSON.parse(risp.point[0].geomjson);
        console.log('AAA');
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
        
        
        Object.entries(risp).forEach(([key, value]) => {
            var divform = document.createElement('div');
            divform.setAttribute('class', 'form-check form-check-inline');
            var checkbox = document.createElement('input');
            checkbox.setAttribute('type', 'checkbox');
            checkbox.setAttribute('class', 'form-check-input');
            checkbox.setAttribute('name', 'id_isol[' + risp[key].id_appe + ']');
            checkbox.setAttribute('value', risp[key].id_appe);
            var label = document.createElement('label');
            label.setAttribute('class', 'form-check-label');
            label.append('class', risp[key].cod_appe + " " + risp[key].desc_prod);
            console.log(risp);
            divform.append(checkbox);
            divform.append(label);
            
            //divform.append('<input type="checkbox" class="form-check-input"   name="id_isol[' + risp[key].id_appe + ']" value="' + risp[key].id_appe + '">');
            //divform.append('<label class="form-check-label" for="inlineCheckbox1">' + risp[key].cod_appe + " " + risp[key].desc_prod + '</label>');
            $('#container').append(divform);
            $('#loader').hide();
        });
    });
}
function changeEvent(ricevuto) {    
        /*
         * Cancellazione dei dati 
         */
        $("#DOSE_HA").val("");
        $("#DOSE_UTILIZZATA").val("");
        $("#TEMPO_RIENTRO").val("");
        $("#VOLUME_L_HA").val("");
        $("#VOLUME_ACQUA_UTILIZZATA").val("");
        $("#AUTORIZZAZIONE_TECNICA").val("");
        $("#ADDETTO_AL_TRATTAMENTO").val("");
        $("#METODO_MACCHINA").val("");
        $("#ACQUA_RISCIACQUO_ECCESSO").val("");
    
        $("#STADIO_FENOLOGICO").val("");
        $('#STADIO_FENOLOGICO').empty();
        $("#AVVERSITA").val("");
        $("#PRINCIPIO_ATTIVO").val("");
        
        $("#ID_OPERAZIONE").val("");
        $("#COMPOSIZIONE_AZOTO").val("");
        $("#COMPOSIZIONE_FOSFORO").val("");
        $("#COMPOSIZIONE_POTASSIO").val("");
        
        $("#DATA_INTERVENTO_START").val("");
        $("#DATA_INTERVENTO_END").val("");
        
        $("#QUANTITA_RACCOLTA").val("");
        $("#DATA_RACCOLTA_START").val("");
        $("#DATA_RACCOLTA_END").val("");
        
        $("#ID_PRODOTTO_FITOSANITARIO").val("");
        $("#QUANTITA_UTILIZZATA").val("");
        $('#TIPO_PRELIEVO_ACQUA').val("");
    
    
    if (ricevuto.value == DIFESA) {
        $("#div_ProdottiFitosanitari").show();
        $("#div_ProdottiFertilizzanti").hide();
        $("#div_quantita_misura").show();
        $(".difesa").show();
        $("#difesa_nutrizione").show();
        $(".nutrizione").hide();
        $(".irrigazione").hide();
        $(".operazione").hide();
        $(".raccolta").hide();
        
        getFasiFenologiche();   
        /*
         * Obbligatorietà dei campi 
         */
        //difesa
        $("#ProdottiFitosanitari").prop('required', true);
        $("#QUANTITA_UTILIZZATA").prop('required', true);
        $("#STADIO_FENOLOGICO").prop('required', true);
        $("#AVVERSITA").prop('required', true);
        $("#PRINCIPIO_ATTIVO").prop('required', true);

        // nutrizione
        $("#ID_OPERAZIONE").prop('required', false);
        $("#COMPOSIZIONE_AZOTO").prop('required', false);
        $("#COMPOSIZIONE_FOSFORO").prop('required', false);
        $("#COMPOSIZIONE_POTASSIO").prop('required', false);

        //operazione
        $("#DATA_INTERVENTO_START").prop('required', false);
        $("#DATA_INTERVENTO_END").prop('required', false);
        //raccolta
        $("#DATA_RACCOLTA_START").prop('required', false);
        $("#DATA_RACCOLTA_END").prop('required', false);
        $("#QUANTITA_RACCOLTA").prop('required', false);
        //irrigazione
        $("#DURATA_IRRIGAZIONE").prop('required', false);
        $("#PORTATA_IRRIGAZIONE").prop('required', false);
        $("#QUANTITA_IRRIGAZIONE").prop('required', false);
        $("#TIPO_PRELIEVO_ACQUA").prop('required', false);
        $('#TIPO_PRELIEVO_ACQUA').val('');

        $("#DOSE_HA").prop('required', true);
        $("#DOSE_UTILIZZATA").prop('required', true);
        $("#TEMPO_RIENTRO").prop('required', true);
        $("#VOLUME_L_HA").prop('required', true);
        $("#VOLUME_ACQUA_UTILIZZATA").prop('required', true);
        $("#AUTORIZZAZIONE_TECNICA").prop('required', true);
        $("#ADDETTO_AL_TRATTAMENTO").prop('required', true);
        $("#METODO_MACCHINA").prop('required', true);
        $("#ACQUA_RISCIACQUO_ECCESSO").prop('required', true);

        $("#ID_PRODOTTO_FITOSANITARIO").prop('required', true);
        $("#QUANTITA_UTILIZZATA").prop('required', true);
        $('#unita_misura_sigla').val('');
        $('#unita_misura').val('');
        $('#TIPO_ARTICOLO').val(CLASSE_FITOSANITARI);

        /*
         * fine
         */


    } else if (ricevuto.value == NUTRIZIONE) {
        $("#div_ProdottiFitosanitari").hide();
        $("#div_ProdottiFertilizzanti").show();
        $("#div_quantita_misura").show();
        $("#difesa_nutrizione").show();
        $(".difesa").hide();
        $(".nutrizione").show();
        $(".irrigazione").hide();
        $(".operazione").hide();
        $(".raccolta").hide();
        
        /*
         * Obbligatorietà dei campi 
         */
        // difesa
        $("#STADIO_FENOLOGICO").prop('required', false);
        $("#AVVERSITA").prop('required', false);
        $("#PRINCIPIO_ATTIVO").prop('required', false);

        // nutrizione
        $("#ID_OPERAZIONE").prop('required', true);
        $("#COMPOSIZIONE_AZOTO").prop('required', true);
        $("#COMPOSIZIONE_FOSFORO").prop('required', true);
        $("#COMPOSIZIONE_POTASSIO").prop('required', true);

        //operazione
        $("#DATA_INTERVENTO_START").prop('required', false);
        $("#DATA_INTERVENTO_END").prop('required', false);
        //raccolta
        $("#QUANTITA_RACCOLTA").prop('required', false);
        $("#DATA_RACCOLTA_START").prop('required', false);
        $("#DATA_RACCOLTA_END").prop('required', false);
        //irrigazione
        $("#DURATA_IRRIGAZIONE").prop('required', false);
        $("#PORTATA_IRRIGAZIONE").prop('required', false);
        $("#QUANTITA_IRRIGAZIONE").prop('required', false);
        $("#TIPO_PRELIEVO_ACQUA").prop('required', false);
        $('#TIPO_PRELIEVO_ACQUA').val('');



        $("#DOSE_HA").prop('required', true);
        $("#DOSE_UTILIZZATA").prop('required', true);
        $("#TEMPO_RIENTRO").prop('required', true);
        $("#VOLUME_L_HA").prop('required', true);
        $("#VOLUME_ACQUA_UTILIZZATA").prop('required', true);
        $("#AUTORIZZAZIONE_TECNICA").prop('required', true);
        $("#ADDETTO_AL_TRATTAMENTO").prop('required', true);
        $("#METODO_MACCHINA").prop('required', true);
        $("#ACQUA_RISCIACQUO_ECCESSO").prop('required', true);

        $("#ID_PRODOTTO_FITOSANITARIO").prop('required', true);
        $("#QUANTITA_UTILIZZATA").prop('required', true);

        $('#unita_misura_sigla').val('');
        $('#unita_misura').val('');
        $('#TIPO_ARTICOLO').val(CLASSE_FERTILIZZANTI);


    } else if (ricevuto.value == IRRIGAZIONE) {
        $("#div_ProdottiFitosanitari").hide();
        $("#div_ProdottiFertilizzanti").hide();
        $("#difesa_nutrizione").hide();
        $("#div_quantita_misura").hide();
        $(".difesa").hide();
        $(".nutrizione").hide();
        $(".irrigazione").show();
        $(".operazione").hide();
        $(".raccolta").hide();
        
        /*
         * 
         */
        // difesa
        $("#STADIO_FENOLOGICO").prop('required', false);
        $("#AVVERSITA").prop('required', false);
        $("#PRINCIPIO_ATTIVO").prop('required', false);

        // nutrizione
        $("#ID_OPERAZIONE").prop('required', false);
        $("#COMPOSIZIONE_AZOTO").prop('required', false);
        $("#COMPOSIZIONE_FOSFORO").prop('required', false);
        $("#COMPOSIZIONE_POTASSIO").prop('required', false);

        //operazione
        $("#DATA_INTERVENTO_START").prop('required', false);
        $("#DATA_INTERVENTO_END").prop('required', false);
        //raccolta
        $("#QUANTITA_RACCOLTA").prop('required', false);
        $("#DATA_RACCOLTA_START").prop('required', false);
        $("#DATA_RACCOLTA_END").prop('required', false);
        //irrigazione
        $("#DURATA_IRRIGAZIONE").prop('required', true);
        $("#PORTATA_IRRIGAZIONE").prop('required', true);
        $("#QUANTITA_IRRIGAZIONE").prop('required', true);
        $("#TIPO_PRELIEVO_ACQUA").prop('required', true);
        $('#TIPO_PRELIEVO_ACQUA').val('');

        $("#DOSE_HA").prop('required', false);
        $("#DOSE_UTILIZZATA").prop('required', false);
        $("#TEMPO_RIENTRO").prop('required', false);
        $("#VOLUME_L_HA").prop('required', false);
        $("#VOLUME_ACQUA_UTILIZZATA").prop('required', false);
        $("#AUTORIZZAZIONE_TECNICA").prop('required', false);
        $("#ADDETTO_AL_TRATTAMENTO").prop('required', false);
        $("#METODO_MACCHINA").prop('required', false);
        $("#ACQUA_RISCIACQUO_ECCESSO").prop('required', false);

        $("#ID_PRODOTTO_FITOSANITARIO").prop('required', false);
        $("#QUANTITA_UTILIZZATA").prop('required', false);
        $('#TIPO_ARTICOLO').val('');



    } else if (ricevuto.value == OPERAZIONE) {
        $("#div_ProdottiFitosanitari").hide();
        $("#div_ProdottiFertilizzanti").hide();
        $("#difesa_nutrizione").hide();
        $("#div_quantita_misura").hide();
        $(".difesa").hide();
        $(".nutrizione").hide();
        $(".irrigazione").hide();
        $(".operazione").show();
        $(".raccolta").hide();
        
        /*
         * 
         */

        // difesa
        $("#STADIO_FENOLOGICO").prop('required', false);
        $("#AVVERSITA").prop('required', false);
        $("#PRINCIPIO_ATTIVO").prop('required', false);

        // nutrizione
        $("#ID_OPERAZIONE").prop('required', false);
        $("#COMPOSIZIONE_AZOTO").prop('required', false);
        $("#COMPOSIZIONE_FOSFORO").prop('required', false);
        $("#COMPOSIZIONE_POTASSIO").prop('required', false);

        //operazione
        $("#DATA_INTERVENTO_START").prop('required', true);
        $("#DATA_INTERVENTO_END").prop('required', true);
        //raccolta
        $("#QUANTITA_RACCOLTA").prop('required', false);
        $("#DATA_RACCOLTA_START").prop('required', false);
        $("#DATA_RACCOLTA_END").prop('required', false);
        //irrigazione
        $("#DURATA_IRRIGAZIONE").prop('required', false);
        $("#PORTATA_IRRIGAZIONE").prop('required', false);
        $("#QUANTITA_IRRIGAZIONE").prop('required', false);
        $("#TIPO_PRELIEVO_ACQUA").prop('required', false);
        $('#TIPO_PRELIEVO_ACQUA').val('');


        $("#DOSE_HA").prop('required', false);
        $("#DOSE_UTILIZZATA").prop('required', false);
        $("#TEMPO_RIENTRO").prop('required', false);
        $("#VOLUME_L_HA").prop('required', false);
        $("#VOLUME_ACQUA_UTILIZZATA").prop('required', false);
        $("#AUTORIZZAZIONE_TECNICA").prop('required', false);
        $("#ADDETTO_AL_TRATTAMENTO").prop('required', false);
        $("#METODO_MACCHINA").prop('required', false);
        $("#ACQUA_RISCIACQUO_ECCESSO").prop('required', false);

        $("#ID_PRODOTTO_FITOSANITARIO").prop('required', false);
        $("#QUANTITA_UTILIZZATA").prop('required', false);
        $('#TIPO_ARTICOLO').val('');

    } else if (ricevuto.value == RACCOLTA) {
        $("#div_ProdottiFitosanitari").hide();
        $("#div_ProdottiFertilizzanti").hide();
        $("#difesa_nutrizione").hide();
        $("#div_quantita_misura").hide();
        $(".difesa").hide();
        $(".nutrizione").hide();
        $(".irrigazione").hide();
        $(".operazione").hide();
        $(".raccolta").show();
        
        /*
         * 
         */

        // difesa
        $("#STADIO_FENOLOGICO").prop('required', false);
        $("#AVVERSITA").prop('required', false);
        $("#PRINCIPIO_ATTIVO").prop('required', false);

        // nutrizione
        $("#ID_OPERAZIONE").prop('required', false);
        $("#COMPOSIZIONE_AZOTO").prop('required', false);
        $("#COMPOSIZIONE_FOSFORO").prop('required', false);
        $("#COMPOSIZIONE_POTASSIO").prop('required', false);

        //operazione
        $("#DATA_INTERVENTO_START").prop('required', false);
        $("#DATA_INTERVENTO_END").prop('required', false);
        //raccolta
        $("#QUANTITA_RACCOLTA").prop('required', true);
        $("#DATA_RACCOLTA_START").prop('required', true);
        $("#DATA_RACCOLTA_END").prop('required', true);
        //irrigazione
        $("#DURATA_IRRIGAZIONE").prop('required', false);
        $("#PORTATA_IRRIGAZIONE").prop('required', false);
        $("#QUANTITA_IRRIGAZIONE").prop('required', false);
        $("#TIPO_PRELIEVO_ACQUA").prop('required', false);
        $('#TIPO_PRELIEVO_ACQUA').val('');

        $("#DOSE_HA").prop('required', false);
        $("#DOSE_UTILIZZATA").prop('required', false);
        $("#TEMPO_RIENTRO").prop('required', false);
        $("#VOLUME_L_HA").prop('required', false);
        $("#VOLUME_ACQUA_UTILIZZATA").prop('required', false);
        $("#AUTORIZZAZIONE_TECNICA").prop('required', false);
        $("#ADDETTO_AL_TRATTAMENTO").prop('required', false);
        $("#METODO_MACCHINA").prop('required', false);
        $("#ACQUA_RISCIACQUO_ECCESSO").prop('required', false);

        $("#ID_PRODOTTO_FITOSANITARIO").prop('required', false);
        $("#QUANTITA_UTILIZZATA").prop('required', false);
        
        $('#TIPO_ARTICOLO').val('');
        

    } else {
        $("#div_quantita_misura").hide();
        $("#div_ProdottiFitosanitari").hide();
        $("#div_ProdottiFertilizzanti").hide();
        $("#difesa_nutrizione").hide();
        
        /*
         * 
         */

        // difesa
        $("#STADIO_FENOLOGICO").prop('required', false);
        $("#AVVERSITA").prop('required', false);
        $("#PRINCIPIO_ATTIVO").prop('required', false);

        // nutrizione
        $("#ID_OPERAZIONE").prop('required', false);
        $("#COMPOSIZIONE_AZOTO").prop('required', false);
        $("#COMPOSIZIONE_FOSFORO").prop('required', false);
        $("#COMPOSIZIONE_POTASSIO").prop('required', false);


        $("#DATA_INTERVENTO_START").prop('required', false);
        $("#DATA_INTERVENTO_END").prop('required', false);
        //raccolta
        $("#QUANTITA_RACCOLTA").prop('required', false);
        $("#DATA_RACCOLTA_START").prop('required', false);
        $("#DATA_RACCOLTA_END").prop('required', false);

        $("#DOSE_HA").prop('required', false);
        $("#DOSE_UTILIZZATA").prop('required', false);
        $("#TEMPO_RIENTRO").prop('required', false);
        $("#VOLUME_L_HA").prop('required', false);
        $("#VOLUME_ACQUA_UTILIZZATA").prop('required', false);
        $("#AUTORIZZAZIONE_TECNICA").prop('required', false);
        $("#ADDETTO_AL_TRATTAMENTO").prop('required', false);
        $("#METODO_MACCHINA").prop('required', false);
        $("#ACQUA_RISCIACQUO_ECCESSO").prop('required', false);

        $("#ID_PRODOTTO_FITOSANITARIO").prop('required', false);
        $("#QUANTITA_UTILIZZATA").prop('required', false);
        $("#TIPO_PRELIEVO_ACQUA").prop('required', false);
        
        $(".difesa").hide();
        $(".nutrizione").hide();
        $(".irrigazione").hide();
        $(".operazione").hide();
        $(".raccolta").hide();
        
        $('#TIPO_ARTICOLO').val('');
    }
}
function goMovimento(azienda, prg_scheda) {
    var object = {
        azienda: azienda,
        prg_scheda: prg_scheda
    };
    $.redirect("registro_tracciamenti/registro_tracciamenti.php", object);
}
function goReg(azienda, prg_scheda) {
    var object = {
        azienda: azienda,
        prg_scheda: prg_scheda
    };
    $.redirect(HTTP_PRIVATE_SECTION + 'registro_tracciamenti.php', object);
}

function changeLabel(key) {
    var intestazione = "";

    if (key == "") {
    } else if (key == "TIPO_INTERVENTO") {
        intestazione = "Tipo intervento : ";
    } else if (key == "DATA_INTERVENTO") {
        intestazione = "Data intervento : ";
    } else if (key == "DATA_FINE_INTERVENTO") {
        intestazione = "Data fine intervento : ";
    } else if (key == "VOLUME_L_HA") {
        intestazione = "Volume l/ha : ";
    } else if (key == "VOLUME_ACQUA_UTILIZZATA") {
        intestazione = "Volume Acqua Utilizzata : ";
    } else if (key == "AUTORIZZAZIONE_TECNICA") {
        intestazione = "Autorizzazione Tecnica : ";
    } else if (key == "ADDETTO_AL_TRATTAMENTO") {
        intestazione = "Addetto Al trattamento : ";
    } else if (key == "METODO_MACCHINA") {
        intestazione = "Metodo/ Macchina : ";
    } else if (key == "ACQUA_RISCIACQUO_ECCESSO") {
        intestazione = "Acqua risciacquo eccesso : ";
    } else if (key == "NOTE") {
        intestazione = "Note : ";
    } else if (key == "STADIO_FENOLOGICO") {
        intestazione = "Stadio Fenologico : ";
    } else if (key == "PORTATA_IRRIGAZIONE") {
        intestazione = "Portata Irrigazione : ";
    } else if (key == "QUANTITA_IRRIGAZIONE") {
        intestazione = "Quantità irrigazione : ";
    } else if (key == "DURATA_IRRIGAZIONE") {
        intestazione = "Durata Irrigazione : ";
    } else if (key == "DATA_FINE_IRRIGAZIONE") {
        intestazione = "Data fine irrigazione : ";
    } else if (key == "DATA_INTERVENTO_START") {
        intestazione = "Data di avvio intervento : ";
    } else if (key == "DATA_INTERVENTO_END") {
        intestazione = "Data fine intervento: ";
    } else if (key == "AVVERSITA") {
        intestazione = "Avversità : ";
    } else if (key == "ID_PRODOTTO_FITOSANITARIO") {
        intestazione = "Prdotto Fitosanitario : ";
    } else if (key == "DOSE_HA") {
        intestazione = "Dose Ha : ";
    } else if (key == "DOSE_UTILIZZATA") {
        intestazione = "Dose utilizzata : ";
    } else if (key == "TEMPO_RIENTRO") {
        intestazione = "Tempo rientro : ";
    } else if (key == "INTERVALLO_SICUREZZA") {
        intestazione = "Intervallo Sicurezza : ";
    } else if (key == "COMPOSIZIONE_AZOTO") {
        intestazione = "Composizione Azoto : ";
    } else if (key == "COMPOSIZIONE_FOSFORO") {
        intestazione = "Composizione Fosforo : ";
    } else if (key == "COMPOSIZIONE_POTASSIO") {
        intestazione = "Composizione Potassio : ";
    } else if (key == "SPECIE") {
        intestazione = "Specie : ";
    } else if (key == "QUANTITA_UTILIZZATA") {
        intestazione = "Quantità utilizzata : ";
    } else if (key == "PRINCIPIO_ATTIVO") {
        intestazione = "Principio attivo : ";
    } else if (key == "ID_OPERAZIONE") {
        intestazione = "Operazione : ";
    } else if (key == "QUANTITA_RACCOLTA") {
        intestazione = "Quantità (Kg.) raccolta : ";
    } else if (key == "UNITA_MISURA") {
        intestazione = "Unità di misura : ";
    } else if (key == "TIPO_PRELIEVO_ACQUA") {
        intestazione = "Tipo prelievo: ";
    }
    
    return intestazione;
}

function getFasiFenologiche(){
    
    $('#STADIO_FENOLOGICO').empty();
    var codi_uso = $('#SPECIE_TXT').val();
    var object = {
        'module': 'services_safe',
        'action': 'listFasi',
        'codi_uso': codi_uso
    };
    postdataClassic(WS_CALL, object, function (response) {
        var risp = jQuery.parseJSON(response);
        
        $.each(risp, function (i, item) {
            $('#STADIO_FENOLOGICO').append($('<option>', { 
                value: item.faseFeno,
                text : item.faseFeno 
            }));
        });        

    });
}

function deleteTreatment() {
    var treatment = $('#elementDelete').val();

    var object = {
        'module': 'registro_tracciamenti',
        'action': 'deleteLogical',
        'id': treatment

    };
    Swal.fire({
        title: "Attenzione",
        text: "Vuoi eliminare il Trattamento scelto?",
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
                        $('#dettaglioModal').modal('toggle');
//                        loadTrattamentiPage();
//                        loadTrattamentiNutrizione();
//                        loadTrattamentiIrrigazione();
//                        loadTrattamentiOperazione();
                        location.reload();
                    });
                } else {
                    $('#loader').hide();
                    $('#dettaglioModal').modal('toggle');
                    functionSwall('error', risp.erroreDescrizione, "");
                }
            });
        }
    });










}


