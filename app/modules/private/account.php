<!doctype html>
<?php
include '../../lib/api.php';
$state = $LoggedAccount->checkStateEditableFieldPage();
?>
<html lang="en">
    <? include_once ROOT . 'layout/head.php'; ?>
    <body>
        <?
        include_once ROOT . 'layout/header.php';
        ?>
        <main role="main">
            <header class="masthead masthead-page mb-5">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-12 text-center" >
                            <h1>Registrazione Dati Anagrafici</h1><br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <p class="text-justify"> 
                                Prima di procedere alla presentazione dell'istanza di richiesta di Progressione Economica, è necessario in prima istanza compilare la form presente in questa pagina con i propri dati anagrafici, fornendo il consenso alla privacy.
                            </p>
                        </div>
                    </div>
                </div>
                <?
                include_once ROOT . 'layout/header_svg.php';
                ?>
            </header>
            <div class="container">
                <? include_once ROOT . 'layout/loader.php'; ?>
                <form action="javascript:save()" id="form-register" >
                    <input type="hidden" name="module" id="module">
                    <input type="hidden" name="action" id="action">
                    <div class="card mb-2">
                        <div class="card-header">
                            <h6 class="mb-0">
                                Dati SPID
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group row">
                                <div class="col-sm-6">
                                    <label for="CODICE_FISCALE">Codice Fiscale </label>
                                    <input readOnly type="text" class="form-control"  required="" name="CODICE_FISCALE" id="CODICE_FISCALE" value="<?= $LoggedAccount->CODICE_FISCALE ?>">
                                </div>
                                <div class="col-sm-6">
                                    <label for="SPID_CELLULARE">Cellulare</label>
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">+39</div>
                                        </div>
                                        <input readonly type="text" class="form-control"  required="" name="SPID_CELLULARE" id="SPID_CELLULARE" value="<?= $LoggedAccount->Anagrafica->SPID_CELLULARE ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="SPID_CELLULARE">Email</label>
                                    <div class="input-group mb-2">                                       
                                        <input readonly type="text" class="form-control"  required="" name="SPID_EMAIL" id="SPID_EMAIL" value="<?= $LoggedAccount->Anagrafica->SPID_EMAIL ?>">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <label for="SPID_TIPO_DOCUMENTO">Tipo documento</label>
                                    <input readonly type="text" class="form-control"  required="" name="SPID_TIPO_DOCUMENTO" id="SPID_TIPO_DOCUMENTO" value="<?= $LoggedAccount->Anagrafica->SPID_TIPO_DOCUMENTO ?>">
                                </div>
                                <div class="col-sm-4">
                                    <label for="SPID_DOCUMENTO">N.ro Documento</label>
                                    <input readonly type="text" class="form-control"  required="" name="SPID_DOCUMENTO" id="SPID_DOCUMENTO" value="<?= $LoggedAccount->Anagrafica->SPID_DOCUMENTO ?>">
                                </div>                        
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-4">
                                    <label for="SPID_DOCUMENTO_ENTE">Ente rilascio documento</label>
                                    <input readonly type="text" class="form-control"  required="" name="SPID_DOCUMENTO_ENTE" id="SPID_DOCUMENTO_ENTE" value="<?= $LoggedAccount->Anagrafica->SPID_DOCUMENTO_ENTE ?>">
                                </div>
                                <div class="col-sm-4">
                                    <label for="SPID_DOCUMENTO_RILASCIO">Rilascio Documento</label>
                                    <input readonly type="text" class="form-control"  required="" name="SPID_DOCUMENTO_RILASCIO" id="SPID_DOCUMENTO_RILASCIO" value="<?= $LoggedAccount->Anagrafica->SPID_DOCUMENTO_RILASCIO ?>">
                                </div>
                                <div class="col-sm-4">
                                    <label for="SPID_DOCUMENTO_SCADENZA">Scadenza Documento</label>
                                    <input readonly type="text" class="form-control"  required="" name="SPID_DOCUMENTO_SCADENZA" id="SPID_DOCUMENTO_SCADENZA" value="<?= $LoggedAccount->Anagrafica->SPID_DOCUMENTO_SCADENZA ?>">
                                </div>                        
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">                        
                        <?= Account::getFieldAuthConsenso(); ?>                            
                    </div>

                    <? /*
                      <div class="form-group row">
                      <div class="col-2 text-right">
                      <input <?= $state['disabled'] ?> class="form-control " type="checkbox" <?= $LoggedAccount->Anagrafica->DICHIARAZIONE_DELEGA == 1 ? "checked" : "" ?> id="DICHIARAZIONE_DELEGA" name="DICHIARAZIONE_DELEGA" onchange="resetCF()">
                      </div>
                      <div class="col-10">
                      <label for="defaultCheck2">
                      Dirigente/Coordinatore Scolastico differente dall'utente SPID<br>
                      <small>(Dichiaro di accedere alla piattaforma in quanto delegato dal Dirigente/Coordinatore Scolastico dello/degli Istituto/i che andrò a gestire.  Di seguito riporto i dati del Dirigente/Coordinatore:</small>
                      </label>
                      </div>
                      </div>
                     */ ?>

                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label for="NOME">Nome</label><br>                            
                            <input disabled type="text" class="form-control cfinput"  required="" name="NOME" id="NOME" value="<?= $LoggedAccount->Anagrafica->NOME ?>">
                        </div>
                        <div class="col-sm-6">
                            <label for="COGNOME">Cognome</label>
                            <input disabled type="text" class="form-control cfinput"  required="" name="COGNOME" id="COGNOME" value="<?= $LoggedAccount->Anagrafica->COGNOME ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label for="SESSO">Genere</label>
                            <input disabled type="text" class="form-control cfinput"  name="SESSO" id="SESSO" value="<?= $LoggedAccount->Anagrafica->SESSO ?>">

                            <? /*
                              <select <?= $state['disabled'] ?>  class="form-control cfinput" required="" name="SESSO" id="SESSO">
                              <option value=""> - </option>
                              <option <?= $LoggedAccount->Anagrafica->SESSO == 'M' ? 'selected' : '' ?> value="M">Maschile</option>
                              <option <?= $LoggedAccount->Anagrafica->SESSO == 'F' ? 'selected' : '' ?> value="F">Femminile</option>
                              </select>
                             */ ?>
                        </div>
                        <div class="col-sm-6">
                            <label for="DATA_NASCITA">Data di nascita</label>
                            <? /*
                              <input <?= $state['readonly'] ?> type="date" class="form-control cfinput"  required="" name="DATA_NASCITA" id="DATA_NASCITA" value="<?= $LoggedAccount->Anagrafica->DATA_NASCITA ?>">
                             */ ?>
                            <input disabled type="text" class="form-control cfinput"  name="DATA_NASCITA" id="DATA_NASCITA" value="<?= $LoggedAccount->Anagrafica->DATA_NASCITA ?>">
                        </div>
                    </div>
                    <?/*?>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label for="COMUNE_NASCITA"><span class="text-danger">*&nbsp;</span>Comune di nascita</label>
                            <select  <?= $state['readonly'] ?>   style="width: 100%"  required=""  class="form-control cfinput" id="COMUNE_NASCITA" name="COMUNE_NASCITA"></select>
                            <!--input <?= $state['readonly'] ?> type="text" class="form-control"  required="" name="COMUNE_NASCITA" id="COMUNE_NASCITA" value="<?= $LoggedAccount->Anagrafica->COMUNE_NASCITA ?>"-->
                        </div>
                        <div class="col-sm-3">
                            <label for="PROV_NASCITA"><span class="text-danger">*&nbsp;</span>Prov. di nascita</label>
                            <input readonly type="text" class="form-control cfinput"  required="" name="PROV_NASCITA" id="PROV_NASCITA" value="<?= $LoggedAccount->Anagrafica->PROV_NASCITA ?>">
                        </div>
                        <div class="col-sm-3">
                            <label for="CAP_NASCITA"><span class="text-danger">*&nbsp;</span>Cap comune di nascita</label>
                            <input <?= $state['readonly'] ?> autocomplete="off" type="text" class="form-control cfinput"  required="" name="CAP_NASCITA" maxlength="5" id="CAP_NASCITA" value="<?= $LoggedAccount->Anagrafica->CAP_NASCITA ?>">
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label for="COMUNE_RESIDENZA"><span class="text-danger">*&nbsp;</span>Comune residenza</label>
                            <select  <?= $state['disabled'] ?>  required=""  style="width: 100%" class="form-control" id="COMUNE_RESIDENZA" name="COMUNE_RESIDENZA"></select>
                            <!--input <?= $state['readonly'] ?> type="text" class="form-control"  required="" name="COMUNE_RESIDENZA" id="COMUNE_RESIDENZA" value="<?= $LoggedAccount->Anagrafica->COMUNE_RESIDENZA ?>"-->
                        </div>
                        <div class="col-sm-3">
                            <label for="PROV_RESIDENZA"><span class="text-danger">*&nbsp;</span>Prov. residenza</label>
                            <input readonly type="text"  class="form-control"  required="" name="PROV_RESIDENZA" id="PROV_RESIDENZA" value="<?= $LoggedAccount->Anagrafica->PROV_RESIDENZA ?>">
                        </div>
                        <div class="col-sm-3">
                            <label for="CAP_RESIDENZA"><span class="text-danger">*&nbsp;</span>Cap residenza</label>
                            <input <?= $state['readonly'] ?> autocomplete="off" type="text" class="form-control"  required="" name="CAP_RESIDENZA" id="CAP_RESIDENZA" maxlength="5" value="<?= $LoggedAccount->Anagrafica->CAP_RESIDENZA ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-12">
                            <label for="INDIRIZZO_RESIDENZA"><span class="text-danger">*&nbsp;</span>Indirizzo residenza</label><br>
                            <input <?= $state['readonly'] ?> autocomplete="off" type="text" class="form-control"  required="" name="INDIRIZZO_RESIDENZA" id="INDIRIZZO_RESIDENZA" value="<?= $LoggedAccount->Anagrafica->INDIRIZZO_RESIDENZA ?>">
                        </div>
                        <?/* COMMENTATO IL 05/03/2021 SU RICHIESTA DEL CLIENTE A MEZZO EMAIL #44 TRELLO ?>
                        <div class="col-sm-12">
                            <label for="DIPARTIMENTO_REGIONALE"><span class="text-danger">*&nbsp;</span>In servizio presso il Dipartimento Regionale</label><br>
                            <input <?= $state['readonly'] ?>  autocomplete="off" type="text" class="form-control"  required="" name="DIPARTIMENTO_REGIONALE" id="DIPARTIMENTO_REGIONALE" value="<?= $LoggedAccount->Anagrafica->DIPARTIMENTO_REGIONALE ?>">
                        </div>
                        <??>
                        <!--                        <div class="col-sm-4">
                                                    <label for="RECAPITO_ALTERNATIVO">&nbsp;Recapito telefonico alternativo</label><br>
                                                    <div class="input-group mb-2">
                                                        <div class="input-group-prepend">
                                                            <div class="input-group-text">+39</div>
                                                        </div>
                                                        <input <?= $state['readonly'] ?> type="text" class="form-control" name="RECAPITO_ALTERNATIVO" id="RECAPITO_ALTERNATIVO" value="<?= (empty($LoggedAccount->Anagrafica->RECAPITO_DIRIGENTE) ? $LoggedAccount->Anagrafica->SPID_CELLULARE : $LoggedAccount->Anagrafica->RECAPITO_ALTERNATIVO) ?>">
                                                    </div>
                                                </div>-->
                    </div>
                    <? if ($state['stato']) { ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <span class="text-danger small">*&nbsp;Campo obbligatorio</span><br>
                            </div>
                        </div>
                    <? } else { ?>
                        <div class="row">
                            <div class="col-sm-12 text-center">
                                <span class="text-danger">&nbsp;&nbsp;La modifica dei dati anagrafici viene inibita dopo avere presentato l'istanza</span><br>
                            </div>
                        </div> 
                    <? } ?>
                    <?*/?>
                    <hr>                    
                    <div class="form-group row" >
                        <div class="offset-sm-2 col-sm-10 text-right ">
                            <? if ($LoggedAccount->Anagrafica->ID > 0) { ?>
                                <button type="button"  class="btn btn-md btn-danger" onclick="goBack()"><i class="fas fa-undo"></i>&nbsp;Annulla</button>
                            <? } ?>
                            <? if ($state['stato']) { ?>
                                <button type="submit"  class="btn btn-md btn-primary" id="button_view" ><i class="fas fa-save"></i>&nbsp;Salva</button>								
                            <? } ?>
                        </div>
                    </div>
                </form>
            </div>
        </main>
        <? include_once ROOT . 'layout/footer.php'; ?>
    </body>
    <script>
        $(document).ready(function () {
            // $('.cfinput').change(function () {
            // verificaCfDirigente();
            // });
            
            $('#loader').hide();
            $("#COMUNE_NASCITA").select2({
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

            $("#COMUNE_RESIDENZA").select2({
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

<? if ($LoggedAccount->Anagrafica->COMUNE_NASCITA != "") { ?>
                $("#COMUNE_NASCITA").select2("trigger", "select", {
                    data: {
                        id: '<?= addslashes($LoggedAccount->Anagrafica->COMUNE_NASCITA) ?>',
                        text: '<?= addslashes($LoggedAccount->Anagrafica->COMUNE_NASCITA) ?>',
                        cap: '<?= $LoggedAccount->Anagrafica->CAP_NASCITA ?>',
                        codice_provincia: '<?= $LoggedAccount->Anagrafica->PROV_NASCITA ?>'
                    }
                });
    <?
}

if ($LoggedAccount->Anagrafica->COMUNE_RESIDENZA != "") {
    ?>
                $("#COMUNE_RESIDENZA").select2("trigger", "select", {
                    data: {
                        id: '<?= addslashes($LoggedAccount->Anagrafica->COMUNE_RESIDENZA) ?>',
                        text: '<?= addslashes($LoggedAccount->Anagrafica->COMUNE_RESIDENZA) ?>',
                        cap: '<?= $LoggedAccount->Anagrafica->CAP_RESIDENZA ?>',
                        codice_provincia: '<?= $LoggedAccount->Anagrafica->PROV_RESIDENZA ?>'
                    }
                });
<? } ?>

<? if ($LoggedAccount->CountDomande <= 0) { ?>
                $('#form-register :input').each(function () {
                    $(this).change(function () {
                        isModified = true;
                        $(this).addClass('is-valid');  
                    });
                });
<? } ?>
<? if (!$state['stato']) { ?>
                $('#COMUNE_NASCITA').select2({
                    disabled: true
                });
                $('#COMUNE_RESIDENZA').select2({
                    disabled: true
                });
<? } ?>
        });

        var isModified = false;
//        function verificaCfDirigente() {
//            if (checkCampiCF()) {
//
//                var object = {
//                    module: 'account',
//                    action: 'calcolaVerificaCf',
//                    NOME: $("#NOME").val(),
//                    COGNOME: $("#COGNOME").val(),
//                    SESSO: $("#SESSO").val(),
//                    DATA_NASCITA: $("#DATA_NASCITA").val(),
//                    COMUNE_NASCITA: $("#COMUNE_NASCITA").val(),
//                    PROV_NASCITA: $("#PROV_NASCITA").val(),
//                    CAP_NASCITA: $("#CAP_NASCITA").val(),
//                    CODICE_FISCALE_DIRIGENTE: $("#CODICE_FISCALE_DIRIGENTE").val()
//                };
//                $('#loader').show();
//                postdataClassic(WS_CALL, object, function (response) {
//                    var risp = jQuery.parseJSON(response);
//                    $('#loader').hide();
//                    if (risp.esito === 1) {
//                        if ($("#CODICE_FISCALE_DIRIGENTE").val() == "") {
//                            $("#CODICE_FISCALE_DIRIGENTE").val(risp.cf);
//                        } else if ((($('#CODICE_FISCALE_DIRIGENTE').val()).trim()).toUpperCase() != risp.cf) {
//                            functionSwall('error', 'Attenzione il <b>Codice Fiscale</b> del Dirigente/Coordinatore non è valido rispetto ai dati anagrafici inseriti, controllare la correttezza dei dati. Per forzare il salvataggio selezionare il campo <b>Forza codice fiscale</b> e proseguire.', "");
//                        }
//
//                    } else {
//                        $('#loader').hide();
//                        functionSwall('error', risp.erroreDescrizione);
//                    }
//                });
//            }
//
//        }

//        function checkCampiCF() {
//            var ret = false;
//
//            if ($("#NOME").val() != "" &&
//                    $("#COGNOME").val() != "" &&
//                    $("#SESSO").val() != "" &&
//                    $("#DATA_NASCITA").val() != "" &&
//                    $("#COMUNE_NASCITA").val() != "" &&
//                    $("#PROV_NASCITA").val() != "" &&
//                    $("#CAP_NASCITA").val() != "" &&
//                    !$('#FORZATURA_CODICE_FISCALE').prop('checked')) {
//                ret = true;
//            }
//            return ret;
//        }

        $('#COMUNE_NASCITA').on('select2:select', function (e) {
            var data = e.params.data;
            if (data) {
                $("#PROV_NASCITA").val(data.codice_provincia);
                $("#CAP_NASCITA").val(data.cap);
                // verificaCfDirigente();
            } else {
                functionSwall('error', "Selezionare un Comune presente nella lista sottostante!", 'error');
                $("#COMUNE_NASCITA").val("");
            }
        });

        $('#COMUNE_RESIDENZA').on('select2:select', function (e) {
            var data = e.params.data;
            if (data) {
                $("#PROV_RESIDENZA").val(data.codice_provincia);
                $("#CAP_RESIDENZA").val(data.cap);
            } else {
                functionSwall('error', "Selezionare un Comune presente nella lista sottostante!", 'error');
                $("#COMUNE_RESIDENZA").val("");
            }
        });

        function resetCF() {

        }

//        function checkCFDelega() {
//            if ($('#DICHIARAZIONE_DELEGA').prop('checked') && (($('#CODICE_FISCALE').val()).trim()).toUpperCase() == (($('#CODICE_FISCALE_DIRIGENTE').val()).trim()).toUpperCase()) {
//                return false;
//            }
//            if (!$('#DICHIARAZIONE_DELEGA').prop('checked') && (($('#CODICE_FISCALE').val()).trim()).toUpperCase() != (($('#CODICE_FISCALE_DIRIGENTE').val()).trim()).toUpperCase()) {
//                return false;
//            }
//            return true;
//        }

        function save() {
//            if (!checkCFDelega()) {
//                functionSwall('error', 'Verificare conformità tra i dati inseriti e il codice fiscale. Utilizzare il flag <b>"Dirigente/Coordinatore Scolastico differente dall\'utente SPID"</b> solo se si stanno inserendo dati diversi da quelli utilizzati per l\'accesso al sistema.', '');
//            } else {
            $('#loader').show();
            //var checkCF = codiceFISCALE($('#CODICE_FISCALE').val());
            //var checkCFDirigente = codiceFISCALE($('#CODICE_FISCALE_DIRIGENTE').val());
//                var checkCF = CodiceFiscale.validate($('#CODICE_FISCALE').val());
//                var checkCFDirigente = CodiceFiscale.validate($('#CODICE_FISCALE_DIRIGENTE').val());

//                if (checkCF === null && checkCFDirigente === null) {
            var form = document.getElementById('form-register');

            var formInvio = new FormData(form);
            formInvio.set('module', 'account');
            formInvio.set('action', 'saveAnagrafica');
            postdata(WS_CALL, formInvio, function (response) {
                $('#loader').hide();
                var risp = jQuery.parseJSON(response);
                if (risp.esito === 1) {
                    isModified = false;
                    functionSwall('success', 'Operazione effettuata con successo', HTTP_PRIVATE_SECTION + 'dashboard.php');
                } else {
                    functionSwall('error', risp.erroreDescrizione, '');
                }
            });
//                } else {
//                    $('#loader').hide();
//                    functionSwall('error', "Il codice Fiscale inserito non è valido ");
//                }
//            }
        }

        function goBack() {
            if (isModified) {
                Swal.fire({
                    title: "Attenzione",
                    text: "Sicuro di volere procedere ? Eventuali modifiche non salvate andranno perse.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'OK'
                }).then((result) => {
                    if (result.value) {
                        $.redirect(HTTP_PRIVATE_SECTION + "dashboard.php");
                    }
                });
            } else {
                $.redirect(HTTP_PRIVATE_SECTION + "dashboard.php");
            }
        }
    </script>
</html>