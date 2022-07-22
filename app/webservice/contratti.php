<?php
$action=$_REQUEST["action"];
switch($action) {

    case 'list':
        listContratti();
        break;
    case 'saveContratto':
        saveContratto();
        break;
    case 'load':
        load();
        break;
    case 'delete':
        delete();
        break;
    case 'list_rate':
        listRate();
        break;
    case 'pagaRata':
        pagaRata();
        break;
    case 'listAllegati':
        listAllegati();
        break;
    case 'saveAllegato':
        saveAllegato();
        break;
    case 'delete_allegato':
        deleteAllegato();
        break;

}
exit();

function listContratti(){
    global $LoggedAccount;
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value

    $cl = $_POST['columns'];
    $data = array();
    $searchArray = array(
        'ID_PATRIMONIO' => $_POST['id_patrimonio']
    );

    for($i = 0; $i<count($cl); $i++) {
        if($cl[$i]['search']['value']!==""){
            $searchArray[$cl[$i]['data']]= $cl[$i]['search']['value'];
            //echo $cl[$i]['data']." => ".$cl[$i]['search']['value']."</br>";
        }
    }
    $searchQuery = "";
    if(count($searchArray)>0){
        $searchQuery .= " AND ( ";
        $where="";
        foreach ($searchArray as $key => $value) {
            $searchQuery .="";
            $where .= ($where == "" ? "" : " OR ") .$key ." = :".$key;
        }

        $searchQuery .= $where." )";
    }

    //echo $searchQuery;

    ## Search
    if($searchValue != ''){
        $searchQuery .= " AND ( lower( numero ) LIKE :numero) ";
        $searchArray['numero'] = "%$searchValue%";
    }
    //echo $searchQuery;exit();
    $res = Contratti::LoadDataTable($searchQuery, $searchArray, $columnName,$columnSortOrder, $row, $rowperpage);
    //Utils::print_array($res);
    foreach($res['empRecords'] as $row){

        $onclick = 'onclick="goToContratto('.$row['ID'].')"';

        $fnAddMod='<a rel="'.RELUPDATE.'" class="btn btn-sm btn-primary" href="#" '.$onclick.' ><i class="fa fa-edit"></i> </a>';
        $fnDisable = "";
        if($row['CANCELLATO']==0){
            $onclickDisable = 'onclick="realAssetsFramework.takeDelete(\''.$row['ID'].'\',\'contratti\', \'delete\', \'#listContratti\' )"';
            $colorBtnDisable = 'btn-warning';
            $iconBtnDisable = 'fas fa-trash-alt';
            $fnDisable = '<a href="#" '.$onclickDisable.' rel="'.RELUPDATE.'" alt="Elimina contratto"  class="btn btn-sm '.$colorBtnDisable.'" ><i class="'.$iconBtnDisable.'"></i></a>';
        }

        $locatoario = new Locatari($row['ID_LOCATARIO']);
        $data[] = array(
            "ID"=> $row['ID'],
            "NUMERO"=> $row['NUMERO'],
            "LOCATARIO"=> $locatoario->COGNOME." ".$locatoario->NOME ."(".$locatoario->CODICE_FISCALE.")",
            "DATA_CONTRATTO"=> Date::FormatDate($row['DATA_CONTRATTO']),
            "DATA_TERMINE"=> Date::FormatDate($row['DATA_TERMINE']),
            "IMPORTO"=> $row['IMPORTO'],
            "OP"=>$fnAddMod."&nbsp;".$fnDisable
        );
    }
    ## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $res['iTotalRecords'],
        "iTotalDisplayRecords" => $res['iTotalDisplayRecords'],
        "aaData" => $data
    );

    exit(json_encode($response));

}

function saveContratto(){
    global $con;
    $transaction = false;

    $con->db_transactionStart();
    $response = Utils::initDefaultResponse(-999, '');
    // VERIFICO SE SONO NEL PERIODO DELLA PRESENTAZIONE DELLA DOMANDA


    Contratti::checkVerificaDatiPreSalvataggio();
    $contratto = new Contratti($_POST);

    $controlliContratto = $contratto->checkDatiPreSave();
    if ($controlliContratto['checkDati']) {
        $response = $contratto->Save();
        if ($response['esito'] == 1) {
            $response = $contratto->generaRate();
            if ($response['esito'] == 1) {
                $transaction = true;
            }
        }
    } else {
        $response['erroreDescrizione'] = ("Verificare i seguenti campi: " . $controlliContratto['txt_dati_mancanti']);
    }

    if ($transaction) {
        $con->db_transactionCommit();
    } else {
        $con->db_transactionRollback();
    }
    exit(json_encode($response));
}
function delete(){

    $rec = new Contratti($_POST);
    $return = $rec->LogicalDelete();
    exit(json_encode($return));
}

function listRate(){
    global $LoggedAccount;
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value

    $cl = $_POST['columns'];
    $data = array();
    $searchArray = array();

    for($i = 0; $i<count($cl); $i++) {
        if($cl[$i]['search']['value']!==""){
            $searchArray[$cl[$i]['data']]= $cl[$i]['search']['value'];
            //echo $cl[$i]['data']." => ".$cl[$i]['search']['value']."</br>";
        }
    }

    if(count($searchArray)>0){
        $searchQuery .= " AND ( ";
        $where="";
        foreach ($searchArray as $key => $value) {
            $searchQuery .="";
            $where .= ($where == "" ? "" : " OR ") .$key ." = :".$key;
        }

        $searchQuery .= $where." )";
    }

    //echo $searchQuery;

    ## Search
    if($searchValue != ''){
        $searchQuery .= " AND ( lower( numero ) LIKE :numero) ";
        $searchArray['numero'] = "%$searchValue%";
    }
    $searchQuery .= " AND E.CANCELLATO = 0 AND E.ID_CONTRATTO = ".intval($_POST['id_contratto']);
    //echo $searchQuery;exit();
    $res = Events::LoadDataTableCustom($searchQuery, $searchArray, $columnName,$columnSortOrder, $row, $rowperpage);
    //Utils::print_array($res);
    $oggi = new DateTime();
    foreach($res['empRecords'] as $row){
        $dtRata = new DateTime($row['START_DATE']);
        $onclick = 'onclick="realAssetsFramework.takeChargeConfirm(\''.$row['ID'].'\',\'contratti\', \'pagaRata\', \'#listRate\' )"';

        $fnAddMod='<a rel="'.RELUPDATE.'" class="btn btn-sm btn-primary" alt="Paga rata" href="#" '.$onclick.' ><i class="fas fa-euro-sign"></i> </a>';
        $fnDisable = "";
        if($row['CANCELLATO']==0){
            $onclickDisable = 'onclick="realAssetsFramework.takeDelete(\''.$row['ID'].'\',\'contratti\', \'delete\', \'#listContratti\' )"';
            $colorBtnDisable = 'btn-warning';
            $iconBtnDisable = 'fas fa-trash-alt';
            $fnDisable = '<a href="#" '.$onclickDisable.' rel="'.RELUPDATE.'" alt="Elimina contratto"  class="btn btn-sm '.$colorBtnDisable.'" ><i class="'.$iconBtnDisable.'"></i></a>';
        }
        $stato = '';
        if(intval($row['RATA_PAGATA']) > 0 ){
            $stato = '<span class="badge badge-success">Pagata</span>';
        }else if(intval($row['RATA_PAGATA']) == 0 && $dtRata >= $oggi){
            $stato = '<span class="badge badge-warning">In scadenza</span>';
        }else if(intval($row['RATA_PAGATA']) == 0 && $dtRata < $oggi){
            $stato = '<span class="badge badge-danger">Scaduta</span>';
        }

        //$locatoario = new Locatari($row['ID_LOCATARIO']);
        $data[] = array(
            "ID"=> $row['ID'],
            "DATA_SCADENZA"=> Date::FormatDate($row['START_DATE']),
            "IMPORTO_RATA"=> number_format($row['IMPORTO_RATA'], 2, ',', '1'),
            "RATA_PAGATA"=> $stato,
            "OP"=>(intval($row['RATA_PAGATA'])> 0 ? '<span class="text-success"><i class="fas fa-check-circle"></i></span>' : $fnAddMod)
        );
    }
    ## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $res['iTotalRecords'],
        "iTotalDisplayRecords" => $res['iTotalDisplayRecords'],
        "aaData" => $data
    );

    exit(json_encode($response));
}

function pagaRata(){
    $id = Utils::getFromReq("id",0);
    $record = new Events($id);
    $record->RATA_PAGATA = 1;
    $return = $record->Save();
    exit(json_encode($return));
}

function listAllegati(){
    global $LoggedAccount;
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value

    $cl = $_POST['columns'];
    $data = array();
    $searchArray = array(
        'ID_CONTRATTO' => $_POST['id_contratto']
    );

    for($i = 0; $i<count($cl); $i++) {
        if($cl[$i]['search']['value']!==""){
            $searchArray[$cl[$i]['data']]= $cl[$i]['search']['value'];
            //echo $cl[$i]['data']." => ".$cl[$i]['search']['value']."</br>";
        }
    }
    $searchQuery = "";
    if(count($searchArray)>0){
        $searchQuery .= " AND ( ";
        $where="";
        foreach ($searchArray as $key => $value) {
            $searchQuery .="";
            $where .= ($where == "" ? "" : " OR ") .$key ." = :".$key;
        }

        $searchQuery .= $where." )";
    }

    //echo $searchQuery;

    ## Search
    if($searchValue != ''){
        $searchQuery .= " AND ( lower( descrizione ) LIKE :descrizione) ";
        $searchArray['descrizione'] = "%$searchValue%";
    }
    //echo $searchQuery;exit();
    $res = ContrattiDocumenti::LoadDataTable($searchQuery, $searchArray, $columnName,$columnSortOrder, $row, $rowperpage);
    //Utils::print_array($res);
    foreach($res['empRecords'] as $row){

        //$onclick = 'onclick="goToPage(\'page_patrimonio\','.$row['ID'].')"';
        $btn = '<button type="button"  class="btn btn-sm btn-success" onclick="openStreamer(\'' . $row['ID'] . '\');" alt="Download allegato" title="Download"><i class="fas fa-download"></i>&nbsp; Download</button>';
        //$fnAddMod='<a rel="'.RELUPDATE.'" class="btn btn-sm btn-primary" href="#" '.$onclick.' ><i class="fa fa-edit"></i> </a>';
        $fnDisable = "";
        if($row['CANCELLATO']==0){
            $onclickDisable = 'onclick="realAssetsFramework.takeDelete(\''.$row['ID'].'\',\'contratti\', \'delete_allegato\', \'#ListAllegati\' )"';
            $colorBtnDisable = 'btn-warning';
            $iconBtnDisable = 'fas fa-trash-alt';
            $fnDisable = '<a href="#" '.$onclickDisable.' rel="'.RELUPDATE.'" alt="Elimina allegato"  class="btn btn-sm '.$colorBtnDisable.'" ><i class="'.$iconBtnDisable.'"></i>&nbsp; Elimina</a>';
        }


        $data[] = array(
            "ID"=> $row['ID'],
            "DESCRIZIONE"=> $row['DESCRIZIONE'],
            "PATH"=> $row['PATH'],
            "OP"=>$btn."&nbsp;".$fnDisable
        );
    }
    ## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $res['iTotalRecords'],
        "iTotalDisplayRecords" => $res['iTotalDisplayRecords'],
        "aaData" => $data
    );

    exit(json_encode($response));
}


function saveAllegato() {
    global $con, $ESTENSIONI_ALLEGATI, $LoggedAccount, $codiciErroreNoControll;
    $uploadedName = 'documento_doc'; // default $FILES param
    $filenameoriginale = ($_FILES[$uploadedName]['name']);
    $transaction = false;
    $transactionLogs = false;
    /* controlli preliminari */
    $response = File::checkFileAllegato($uploadedName, MAX_SIZE_FILE, $ESTENSIONI_ALLEGATI); /* controllo sul file allegato */
    $con->db_transactionStart();
    if ($response['esito'] == 1) {
        $_POST['DESCRIZIONE'] = Utils::get_filter_string_POST('DESCRIZIONE');
        $_POST['DESCRIZIONE'] = mb_substr(trim($_POST['DESCRIZIONE']), 0, LUNGHEZZA_INDIRIZZO);
        $allegato = new ContrattiDocumenti($_POST);
        $folder = $allegato->generateDirForUpload(ROOT_UPLOAD_DOCUMENTI, ROOT_UPLOAD_DOC);
        if ($folder) {
            $fileName = time() . "_" . File::GetValidFilename($_FILES[$uploadedName]['name']);
            if (is_uploaded_file($_FILES[$uploadedName]['tmp_name'])) {
                $fileFullPath = $folder . "/" . $fileName;
                if (copy($_FILES[$uploadedName]['tmp_name'], $fileFullPath)) {
                    $allegato->PATH = $fileName;
                    $response = $allegato->Save();
                } else {
                    $response['erroreDescrizione'] = "Al momento non è possibile allegare il file, contattare un amministratore! (10996)";
                }
            } else {
                $response['erroreDescrizione'] = "Al momento non è possibile allegare il file, contattare un amministratore! (10995)";
            }
        } else {
            $response['erroreDescrizione'] = "Al momento non è possibile allegare il file, contattare un amministratore! (10994)";
        }

        if ($response['esito'] == 1) {
            $transaction = true;
        } else {
            $response['erroreDescrizione'] = ($response['erroreDescrizione'] != "" ? $response['erroreDescrizione'] : "Al momento non è possibile allegare il file, contattare un amministratore (1521)");
        }
    }


    if ($transaction) {
        $con->db_transactionCommit();
    } else {
        $con->db_transactionRollback();
    }


    exit(json_encode($response));
}

function deleteAllegato(){
    global $con, $LoggedAccount;
    $response = Utils::initDefaultResponse();
    $transaction = false;
    $con->db_transactionStart();
    $allegato = new ContrattiDocumenti($_POST['id']);
    $response = $allegato->deleteAllegato();
    if ($response['esito'] == 1) {
        $transaction = true;
    }

    if ($transaction) {
        $con->db_transactionCommit();
        $response['contratto'] = $allegato->ID_PATRIMONIO;
    } else {
        $con->db_transactionRollback();
    }
    exit(json_encode($response));
}