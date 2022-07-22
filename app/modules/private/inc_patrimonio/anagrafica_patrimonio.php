<div class="card">
    <div class="card-header">
        <h6><?=($patrimonio->ID>0 ? 'Modifica' : 'Nuovo')?> Bene patrimoniale</h6>
    </div>
    <form  id="form-patrimonio" class="needs-validation" action="javascript:savePatrimonio()">
        <div class="card-body">
            <input type="hidden" name="module" id="module">
            <input type="hidden" name="action" id="action">
            <input type="hidden" name="ID" id="ID" value="<?=$patrimonio->ID?>">
            <div class="row row-group p-1">
                <div class="col-lg-3">
                    <label for="NOME" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Nome</small></label>
                    <input   placeholder="Nome del bene" type="text" class="form-control cfinput"  required="" name="NOME" id="NOME" value="<?= $patrimonio->NOME ?>">
                </div>
                <div class="col-lg-3">
                    <label for="DESCRIZIONE" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Descrizione</small></label>
                    <input   placeholder="breve descrizione del bene" type="text" class="form-control cfinput"  required="" name="DESCRIZIONE" id="DESCRIZIONE" value="<?= $patrimonio->DESCRIZIONE ?>">
                </div>
                <div class="col-lg-3">
                    <label for="TIPO_PATRIMONIO" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Tipo</small></label>
                    <select style="width: 100%" required="" class="form-control"  id="TIPO_PATRIMONIO" name="TIPO_PATRIMONIO">
                        <option value="">--</option>
                        <?
                        foreach ($tipo_patrimonio as $value){
                            ?>
                            <option value="<?=$value['ID_CODICE']?>" <?=($patrimonio->TIPO_PATRIMONIO == $value['ID_CODICE'] ? 'selected' : '')?>><?=$value['DESCRIZIONE']?></option>
                        <?}?>
                    </select>
                </div>
                <div class="col-lg-3">
                    <label for="STATO" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Stato</small></label>
                    <select style="width: 100%" required="" class="form-control"  id="STATO" name="STATO">
                        <option value="">--</option>
                        <option value="1" <?=($patrimonio->STATO == 1 ? 'selected' : '')?>>Attivo</option>
                        <option value="2" <?=($patrimonio->STATO == 2 ? 'selected' : '')?>>Dismesso</option>
                        <option value="9" <?=($patrimonio->STATO == 9 ? 'selected' : '')?>>Non attivo</option>
                    </select>
                </div>
            </div>
            <div class="row row-group p-1">
                <div class="col-lg-3">
                    <label for="COMUNE" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Comune</small></label>
                    <select  style="width: 100%"  required=""  class="form-control cfinput" id="COMUNE" name="COMUNE"></select>
                </div>
                <div class="col-lg-2">
                    <label for="PROVINCIA" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Prov.</small></label>
                    <input readonly placeholder="Prov." type="text" class="form-control cfinput"  required="" name="PROVINCIA" id="PROVINCIA" value="<?= $patrimonio->PROVINCIA ?>">
                </div>
                <div class="col-lg-5">
                    <label for="INDIRIZZO" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Indirizzo</small></label>
                    <input   placeholder="Via" type="text" class="form-control cfinput"  required="" name="INDIRIZZO" id="INDIRIZZO" value="<?= $patrimonio->INDIRIZZO ?>">
                </div>
                <div class="col-lg-2">
                    <label for="CIVICO" class="col-form-label"><small class="text-info">Civico</small></label>
                    <input   placeholder="Civico" type="text" class="form-control cfinput" name="CIVICO" id="CIVICO" value="<?= $patrimonio->CIVICO ?>">
                </div>
            </div>
            <div class="row row-group p-1">
                <div class="col-lg-3">
                    <label for="FOGLIO" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Foglio</small></label>
                    <input required="" placeholder="Foglio" type="text" class="form-control cfinput" name="FOGLIO" id="FOGLIO" value="<?= $patrimonio->FOGLIO ?>">
                </div>
                <div class="col-lg-3">
                    <label for="PARTICELLA" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Particella</small></label>
                    <input required="" placeholder="Particella" type="text" class="form-control cfinput" name="PARTICELLA" id="PARTICELLA" value="<?= $patrimonio->PARTICELLA ?>">
                </div>
                <div class="col-lg-3">
                    <label for="SEZIONE" class="col-form-label"><span class="text-danger">*</span>&nbsp;<small class="text-info">Sezione</small></label>
                    <input required="" placeholder="Sezione" type="text" class="form-control cfinput" name="SEZIONE" id="SEZIONE" value="<?= $patrimonio->SEZIONE ?>">
                </div>
                <div class="col-lg-3">
                    <label for="DIMENSIONI" class="col-form-label"><small class="text-info">Dimensioni</small></label>
                    <input placeholder="Dimensioni in mq" type="text" class="form-control cfinput" name="DIMENSIONI" id="DIMENSIONI" value="<?= $patrimonio->DIMENSIONI ?>">
                </div>

            </div>
        </div>
        <div class="card-footer">
            <div class="row ">
                <div class="offset-sm-2 col-sm-10 text-right ">
                    <button style="display: none;" type="button" id="btn-refresh"  class="btn btn-md btn-danger" onclick="goBack()"><i class="fas fa-sync"></i>&nbsp;Annulla modifiche</button>
                    <button type="submit" id="btn-save-patrimonio" class="btn btn-md btn-success"><i class="fas fa-save"></i>&nbsp;Salva</button>

                </div>
            </div>
        </div>
    </form>
</div>
