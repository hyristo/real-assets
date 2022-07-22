<?php
$action=$_REQUEST["action"];
switch($action){

    case 'list':
        list_patrimonio();
        break;
    case 'savePatrimonio':
        savePatrimonio();
        break;
    case 'delete':
        deletePatrimonio();
        break;
    case 'riattiva':
        riattiva();
        break;
    case 'listFoto':
        listFoto();
        break;
    case 'saveFoto':
        saveFoto();
        break;
    case 'delete_foto':
        deleteFoto();
        break;

}

exit();


function list_patrimonio(){
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
    $res = AnagraficaPatrimonio::LoadDataTable($searchQuery, $searchArray, $columnName,$columnSortOrder, $row, $rowperpage);
    //Utils::print_array($res);
    foreach($res['empRecords'] as $row){

        $onclick = 'onclick="goToPage(\'page_patrimonio\','.$row['ID'].')"';

        $fnAddMod='<a rel="'.RELUPDATE.'" class="btn btn-primary" href="#" '.$onclick.' ><i class="fa fa-edit"></i> </a>';

        if($row['CANCELLATO']==0){
            $onclickDisable = 'onclick="realAssetsFramework.takeDelete(\''.$row['ID'].'\',\'patrimonio\', \'delete\', \'#ListPatrimonio\' )"';
            $colorBtnDisable = 'btn-warning';
            $iconBtnDisable = 'far fa-square';
        } else {
            $onclickDisable = 'onclick="realAssetsFramework.takeChargeConfirm(\''.$row['ID'].'\',\'patrimonio\', \'riattiva\', \'#ListPatrimonio\' )"';
            $colorBtnDisable = 'btn-danger';
            $iconBtnDisable = 'far fa-check-square';
        }

        $fnDisable = '<a href="#" '.$onclickDisable.' rel="'.RELUPDATE.'"  class="btn '.$colorBtnDisable.'" ><i class="'.$iconBtnDisable.'"></i></a>';

        $data[] = array(
            "ID"=> $row['ID'],
            "NOME"=> $row['NOME'],
            "INDIRIZZO"=> $row['INDIRIZZO'],
            "COMUNE"=>$row['COMUNE'],
            "PROVINCIA"=>$row['PROVINCIA'],
            "PARTICELLA"=>$row['PARTICELLA'],
            "modifica"=>$fnAddMod,
            "cancellato"=>$fnDisable
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

function savePatrimonio(){
    global $con;
    $transaction = false;

    $con->db_transactionStart();
    $response = Utils::initDefaultResponse(-999, '');
    // VERIFICO SE SONO NEL PERIODO DELLA PRESENTAZIONE DELLA DOMANDA


    AnagraficaPatrimonio::checkVerificaDatiPreSalvataggio();
    $patrimonio = new AnagraficaPatrimonio($_POST);

    $controlliPatrimonio = $patrimonio->checkDatiPreSave();
    if ($controlliPatrimonio['checkDati']) {
        $response = $patrimonio->Save();
        if ($response['esito'] == 1) {
            $transaction = true;
        }
    } else {
        $response['erroreDescrizione'] = ("Verificare i seguenti campi: " . $controlliPatrimonio['txt_dati_mancanti']);
    }

    if ($transaction) {
        $con->db_transactionCommit();
    } else {
        $con->db_transactionRollback();
    }
    exit(json_encode($response));
}

function deletePatrimonio(){
    $id = Utils::getFromReq("id",0);
    $record = new AnagraficaPatrimonio($id);
    $return = $record->LogicalDelete();
    exit(json_encode($return));
}

function riattiva(){
    $id = Utils::getFromReq("id",0);
    $record = new AnagraficaPatrimonio($id);
    $record->CANCELLATO = 0;
    $return = $record->Save();
    exit(json_encode($return));
}

function listFoto(){
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
        $searchQuery .= " AND ( lower( descrizione ) LIKE :descrizione) ";
        $searchArray['descrizione'] = "%$searchValue%";
    }
    //echo $searchQuery;exit();
    $res = FotoPatrimonio::LoadDataTable($searchQuery, $searchArray, $columnName,$columnSortOrder, $row, $rowperpage);
    //Utils::print_array($res);
    foreach($res['empRecords'] as $row){

        //$onclick = 'onclick="goToPage(\'page_patrimonio\','.$row['ID'].')"';

        //$fnAddMod='<a rel="'.RELUPDATE.'" class="btn btn-sm btn-primary" href="#" '.$onclick.' ><i class="fa fa-edit"></i> </a>';
        $fnDisable = "";
        if($row['CANCELLATO']==0){
            $onclickDisable = 'onclick="realAssetsFramework.takeDelete(\''.$row['ID'].'\',\'patrimonio\', \'delete_foto\', \'#ListFoto\' )"';
            $colorBtnDisable = 'btn-warning';
            $iconBtnDisable = 'fas fa-trash-alt';
            $fnDisable = '<a href="#" '.$onclickDisable.' rel="'.RELUPDATE.'" alt="Elimina foto"  class="btn btn-sm '.$colorBtnDisable.'" ><i class="'.$iconBtnDisable.'"></i></a>';
        }


        $data[] = array(
            "ID"=> $row['ID'],
            "DESCRIZIONE"=> $row['DESCRIZIONE'],
            "PATH"=> $row['PATH'],
            "OP"=>$fnDisable
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


function saveFoto() {
    global $con, $ESTENSIONI_FOTO, $LoggedAccount, $codiciErroreNoControll;
    $uploadedName = 'documento_foto'; // default $FILES param
    $filenameoriginale = ($_FILES[$uploadedName]['name']);
    $transaction = false;
    $transactionLogs = false;
    /* controlli preliminari */
    $response = File::checkFileAllegato($uploadedName, MAX_SIZE_FILE, $ESTENSIONI_FOTO); /* controllo sul file allegato */
    $con->db_transactionStart();
    if ($response['esito'] == 1) {
        $_POST['DESCRIZIONE'] = Utils::get_filter_string_POST('DESCRIZIONE');
        $_POST['DESCRIZIONE'] = mb_substr(trim($_POST['DESCRIZIONE']), 0, LUNGHEZZA_INDIRIZZO);
        $allegato = new FotoPatrimonio($_POST);
        $folder = $allegato->generateDirForUpload(ROOT_UPLOAD_FOTO, ROOT_UPLOAD_PHOTO);
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

function deleteFoto(){
    global $con, $LoggedAccount;
    $response = Utils::initDefaultResponse();
    $transaction = false;
    $con->db_transactionStart();
    $allegato = new FotoPatrimonio($_POST['id']);
    $response = $allegato->deleteFoto();
    if ($response['esito'] == 1) {
        $transaction = true;
    }

    if ($transaction) {
        $con->db_transactionCommit();
        $response['patrimonio'] = $allegato->ID_PATRIMONIO;
    } else {
        $con->db_transactionRollback();
    }
    exit(json_encode($response));
}