<?php

$mode = $_REQUEST['action'];
switch ($mode) {
    case "searchComune":
        searchComune();
        break;
    case 'saveAnagrafica':
        saveAnagrafica();
        break;
    case 'calcolaVerificaCf':
        calcolaVerificaCodiceFiscale();
        break;
    case 'checkAuth';
        checkAuth();
        break;
    case 'login':
        login();
        break;    
    case 'sendPassword':
        sendPassword();
        break;
    case 'listaO':
        lista();
        break;
    case 'load':
        load();
        break;
}

function load() {
    global $con;
    $uid_firebase = new UID_FIREBASE_ADMIN(trim(base64_decode($_REQUEST['i'])));
    exit(json_encode($uid_firebase));
}

function login() {    
    global $con, $LoggedAccount;
    $response = Utils::initDefaultResponse();
    $response['bottone'] = 0;
    $response['token'] = 0;
    $response = UID_FIREBASE_ADMIN::Login($_REQUEST['email'], $_REQUEST['password']);
    exit(json_encode($response));
}

function sendPassword() {
    $result = array();
    $email = Utils::get_filter_string_POST('email');
    
    if ($email == "") {
        $result['erroreDescrizione'] = "Attenzione inserire l'indirizzo E-mail";
    } else {
        $risposta_Account = UID_FIREBASE_ADMIN::selectIdFromEmail($email);        
        if($risposta_Account['esito']==1){            
            $resultSendPass = UID_FIREBASE_ADMIN::sendPwdResetFirebase(trim($email));
            if ($resultSendPass['esito'] == 1) {
                $result['esito'] = 1;
            } else {
                $result['esito'] = -999;
                $result['erroreDescrizione'] = $resultSendPass['erroreDescrizione'];
            }
        } else {
            $result['esito'] = -999;
            $result['erroreDescrizione'] = $risposta_Account['erroreDescrizione'];
        }
    }
    exit(json_encode($result));
}

function searchComune() {
    $term = Utils::getFromReq("searchTerm");
    $response = array();
    $response = Comune::autocomplete($term);

    exit(json_encode($response));
}

function saveAnagrafica() {
    global $con, $LoggedAccount, $statoSportello;
    $return = Utils::initDefaultResponse();
    $resAuth = Utils::initDefaultResponse(1);
    $success = false;
    $validateInput = "";
    
    $filteredInput = Utils::requestDati($_POST, array('INDIRIZZO_RESIDENZA'));
    $filteredInput["AUTORIZZAZIONE_TRATTAMENTO"] = Utils::getCheckInputIntValue($filteredInput, "AUTORIZZAZIONE_TRATTAMENTO");
    // Load Dati From DatiPersonali (importati da Regione)
    $filteredInput['CODICE_FISCALE'] = trim($LoggedAccount->CODICE_FISCALE);
    $filteredInput['NOME'] = trim($LoggedAccount->Anagrafica->NOME);
    $filteredInput['COGNOME'] = trim($LoggedAccount->Anagrafica->COGNOME);
    $filteredInput['SESSO'] = trim($LoggedAccount->Anagrafica->SESSO);
    $filteredInput['DATA_NASCITA'] = trim($LoggedAccount->Anagrafica->DATA_NASCITA);
    
    
    
    //
    $con->db_transactionStart();
    if (
            true//Utils::validFieldSave('AccountAnagrafica', $filteredInput, array('ID', 'DATA_MODIFICA', 'RECAPITO_ALTERNATIVO')) && Utils::validSavedAccount("CODICE_FISCALE", $filteredInput["CODICE_FISCALE"]) && Account::checkTrattamento($filteredInput["AUTORIZZAZIONE_TRATTAMENTO"])
    ) {
        if (AccountAnagrafica::verificaDatiSPID($filteredInput)) {/* controllo se i dati SPID presenti nel LoggedAccount (ricevuti da SPID) sono uguali a quelli che vengono salvati per evitare manimissioni */
            $validateInput = Utils::validateInput($_POST, array('CAP_NASCITA' => LUNGHEZZA_CAP, 'CAP_RESIDENZA' =>LUNGHEZZA_CAP)); //inserisco il controllo sui dati con la relativa lunghezza DA poter inserire nelle CONSTANT
            /*
             * Controllo inserito per il cap nascita e residenza 
             */
            if ($validateInput) {
                $anagrafica = new AccountAnagrafica($filteredInput);
                //Utils::print_array($anagrafica);exit();
                if ($anagrafica->ID <= 0) {
                    $account = Account::load($filteredInput["CODICE_FISCALE"]);
                    $resAuth = $account->SaveAuth($filteredInput["AUTORIZZAZIONE_TRATTAMENTO"]);
                }
                if ($resAuth["esito"] == 1) {
                    $return = $anagrafica->Save();
                    if ($return['esito'] == 1) {
                        $success = true;
                    }
                }
            } else {
                $return['erroreDescrizione'] = "Verificare i dati inseriti (365)";
            }
        } else {
            $return['esito'] = -999;
            $return['erroreDescrizione'] = "Verificare i dati inseriti (371)";
        }
    } else {
        $return['esito'] = -999;
        $return['erroreDescrizione'] = "Per proseguire devi autorizzare il trattamento dei dati (REG. UE 679/2016)";
    }
    if ($success) {
        $LoggedAccount->reloadSession();
        $con->db_transactionCommit();
    } else {
        $con->db_transactionRollback();
    }
    
    exit(json_encode($return));
}

function calcolaVerificaCodiceFiscale() {

    $calcola = false;
    $nome = Utils::getFromReq("NOME");
    $cognome = Utils::getFromReq("COGNOME");
    $data = Utils::getFromReq("DATA_NASCITA"); //(Forma: GG/MM/YYYY)
    $sesso = Utils::getFromReq("SESSO"); //'M';
    $comune = Utils::getFromReq("COMUNE_NASCITA"); //'Genova';
    $prov = Utils::getFromReq("PROV_NASCITA"); //'GE';
    $data = Date::FormatDate($data);
    $cf = new CodiceFiscale($nome, $cognome, $data, $sesso, $comune, $prov);
    $codice = $cf->calcolaFrom();
    $response = array(
        "esito" => ($codice != '' ? 1 : -999),
        "cf" => $codice,
        "erroreDescrizione" => ($codice != '' ? '' : 'Errore nel calcolo')
    );
    exit(json_encode($response));
}

function checkAuth(){    
   $access = Utils::canAccess();
   $response = array(
        "esito" => ($access ? 1 : -999),
        "erroreDescrizione" => ($access != '' ? '' : 'Accesso scaduto: si prega di aggiornare la pagina ed effettuare il login al sistema.')
   );
   exit(json_encode($response));
}


function lista() {
    global $LoggedAccount;
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $searchValue = $_POST['search']['value']; // Search value
    $data = array();
    $searchArray = array();
## Search     
    $searchQuery = " ";
    if ($searchValue != '') {
        /*
         * modificare queste righe per visualizzare le ricerche
         */
        $searchQuery .= " AND ( COGNOME LIKE :COGNOME ) || ( NOME LIKE :COGNOME ) ";
        $searchArray = array(
            'COGNOME' => "%$searchValue%"
//            'username' => "%$searchValue%"
        );
    }
    $res = UID_FIREBASE_ADMIN::LoadDataTable($searchQuery, $searchArray, $columnName, $columnSortOrder, $row, $rowperpage);
    foreach ($res['empRecords'] as $row) {
        $modifica = '<a onclick="view_edit(' . trim($row['ID']) . ')" data-toggle="modal" data-target="#edit-operatore" class="btn btn-warning btn-sm"><span class="text-white"><i class="fa fa-users"></i>&nbsp;Modifica</a></span>';
//        $delete = '<a class="btn btn-danger btn-sm" href="ciolla" ><i class="fa fa-times"></i>&nbsp;Elimina</a>';
        $data[] = array(
            "NOME" => $row['NOME'],
            "COGNOME" => $row['COGNOME'],
            "EMAIL" => $row['EMAIL'],
            "MODIFICA" => $modifica,
//            "ELIMINA" => $delete
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