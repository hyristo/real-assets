<div class="row">
    <div class="col-6">
        <div class="row">
            <div class="col-sm-12">
                <button type="button"  class="btn btn-sm btn-primary" type="button" data-toggle="modal" data-target="#allegatiModal" alt="Allega foto" title="Allega foto"><i class="fas fa-paper-plane"></i>&nbsp; Allega</button>
                <table id="ListFoto" class="table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Descrizione</th>
                            <th>Path</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </div>
    <div class="col-6">
        <div class="card" id="viewFoto">
            <div class="card-body">
                <p class="card-text">Selezionare una riga dalla griglia per visualizzare la relativa immagine</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="allegatiModal" tabindex="-1" role="dialog" aria-labelledby="allegatiModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 70%;"  >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allegatiModalLabel">Allega documento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-allegati" action="javascript:saveFoto()" >
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <p><i>Selezionare il file che si desidera caricare a sistema.<br> <b>Formati allegati ammessi: &nbsp;&nbsp;(<?=ESTNSIONI_FOTO_TXT?>)</b></i></p>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <input type="hidden" name="ID" id="ID">
                            <input type="hidden" name="ID_PATRIMONIO" id="ID_PATRIMONIO" value="<?=$patrimonio->ID?>">
                            <input type="hidden" name="module" id="module">
                            <input type="hidden" name="action" id="action">
                            <input class="form-control" type="file" id="documento_foto" name="documento_foto" required>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 text-center">
                            <small class="text-danger"> <i>Attenzione dimensione massima del file <?= MAX_SIZE_FILE_TEXT ?> MB (Megabyte)</i></small>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="mb-3">
                                <textarea class="form-control" id="DESCRIZIONE" name="DESCRIZIONE"  maxlength="<?= LUNGHEZZA_INDIRIZZO ?>" placeholder="Aggiungi un descrizione"></textarea>
                                <p class="text-center"><small class="text-danger"> <i>Attenzione il limite massimo di caratteri è <?= LUNGHEZZA_INDIRIZZO ?></i>
                                    </small></p>
                            </div>
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
