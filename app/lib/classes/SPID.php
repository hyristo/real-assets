<?php

/**
 * Description of SPID
 *
 * @author Gigi
 */
class SPID extends DataClass {
    
    public static function checkNumberParamsSpid() {
        if (!FAKE_SPID && count($_POST) < 3){
            Header('Location: '.BASE_HTTP.'index.php');
        }
    }
    /*
     * Funzione per la verifica dei parametri ricevuti in post e il controllo di veriticità del login
     */

    public static function checkLogLoginSpid() {
        global $con;
        $response = false;
        if (count($_POST) > 3) {
            $fiscalNumberTINIT = explode("-", $_POST['fiscalNumber']);
            $fiscalNumber = $fiscalNumberTINIT[1];
            $mobilePhone = trim($_POST['mobilePhone']);
            $email = trim($_POST['email']);
            $idCard = trim($_POST['idCard']);
            $inResponseTo = trim($_POST['inResponseTo']);
            $AuthnInstantExplode = explode("-", $_POST['AuthnInstant']);
            $AuthnInstant = $AuthnInstantExplode[1];
            if (!empty($fiscalNumber) && !empty($inResponseTo) && !empty($AuthnInstant) && strlen($fiscalNumber) == 16) {
                $verifySpid = AccessLog::selectLog($fiscalNumber, $inResponseTo, $AuthnInstant, RETURNTOSPID);
                if ($verifySpid) {
                    $response = array(
                        "fiscalNumber" => $fiscalNumber,
                        "mobilePhone" => $mobilePhone,
                        "email" => $email,
                        "idCard" => $idCard,
                        "inResponseTo" => $inResponseTo,
                        "AuthnInstant" => $AuthnInstant
                    );
                }
            }
        }
        return $response;
    }
    
     public static function getFakeSPIDAccount() {
        $account = new Account();
        $fiscalNumberbase = CF_SPID_TEST;
        $fiscalNumberTINIT = explode("-", $fiscalNumberbase);
        $account->CODICE_FISCALE = $fiscalNumberTINIT[1];//MNDLCU98T68C933T
        $account->ID_ENTE = intval($fiscalNumberTINIT[2]);
        $account->Anagrafica->CODICE_FISCALE = $fiscalNumberTINIT[1];//MNDLCU98T68C933T
        $account->Anagrafica->NOME = "Tizio2";
        $account->Anagrafica->COGNOME = "Ferraro2";
        $account->Anagrafica->SESSO = "M";
        $account->Anagrafica->DATA_NASCITA = "2020-10-20";
        $account->Anagrafica->COMUNE_NASCITA = "MESSINA";
        $account->Anagrafica->PROV_NASCITA = "ME";
        $account->Anagrafica->CAP_NASCITA = "95010";
        $account->Anagrafica->COMUNE_RESIDENZA = "PALERMO";
        $account->Anagrafica->PROV_RESIDENZA = "PA";
        $account->Anagrafica->CAP_RESIDENZA = "96100";
        $account->Anagrafica->INDIRIZZO_RESIDENZA = "VIA LITRI";
        $account->Anagrafica->CIVICO_RESIDENZA = "11";
        $account->Anagrafica->SPID_CELLULARE = "33333333";
        $account->Anagrafica->SPID_EMAIL = "CASCIARO@GMAIL.COM";
        $account->Anagrafica->SPID_DOCUMENTO = "AS09452389";
        $account->Anagrafica->SPID_DOCUMENTO_SCADENZA = "02/01/2013";
        $account->Anagrafica->SPID_DOCUMENTO_RILASCIO = "31/01/2013";
        $account->Anagrafica->SPID_TIPO_DOCUMENTO = "CartaIdentita'";
        $account->Anagrafica->SPID_DOCUMENTO_ENTE = "ComuneRoma";
        $account->AUTORIZZAZIONE_TRATTAMENTO = 0;
        return $account;
    }
    
    public static function loginFake($input = array()) {
        global $con;
        $fiscalNumberbase = CF_SPID_TEST;
        $fiscalNumberTINIT = explode("-", $fiscalNumberbase);
        $fiscalNumber = $fiscalNumberTINIT[1];
        
        $rappresentanteLegale = new RappresentanteLegale();        
        $anagraficaRappresentante = $rappresentanteLegale->GetAnagraficaSoggetto($fiscalNumber);
        $idUfficioCAAF = intval($fiscalNumberTINIT[2]);
        
        //Utils::print_array($ufficioCaaf);exit();
        $id_account = ($idUfficioCAAF > 0 ? "CAA-".$idUfficioCAAF : $anagraficaRappresentante->codi_fisc);
        $LoggedAccount = Account::load($id_account);
        $LoggedAccount->ID_ENTE = $idUfficioCAAF;
        
        
        $ufficioCaaf = new AnagraficaUffici($idUfficioCAAF);
        
        if ($LoggedAccount->ID <= 0) {
            
            $LoggedAccount->CODICE_FISCALE = ($ufficioCaaf->id_ufficio > 0 ? "CAA-".$ufficioCaaf->id_ufficio : $anagraficaRappresentante->codi_fisc);
            $LoggedAccount->ID_GRUPPO = ($ufficioCaaf->id_ufficio > 0 ? GRUPPO_CAA : GRUPPO_AZIENDE);
            $LoggedAccount->AUTORIZZAZIONE_TRATTAMENTO = 1;
            
            //$LoggedAccount = self::getFakeSPIDAccount();
            //$LoggedAccount->CODICE_FISCALE = $fiscalNumber;
            $resAccount = $LoggedAccount->Save();            
             
            
            
            if($resAccount['esito']){
                
                $anagrafica = new AccountAnagrafica();
                $anagrafica->CODICE_FISCALE = ($idUfficioCAAF > 0 ? "CAA-".$ufficioCaaf->id_ufficio : $anagraficaRappresentante->codi_fisc);
                $anagrafica->NOME = ($idUfficioCAAF > 0 ? $ufficioCaaf->descruff : $anagraficaRappresentante->desc_nome);
                $anagrafica->COGNOME = ($idUfficioCAAF > 0 ? $ufficioCaaf->descrestuff : $anagraficaRappresentante->desc_cogn);
                $anagrafica->SESSO = ($idUfficioCAAF > 0 ? "G" : $anagraficaRappresentante->codi_sess);
                if($idUfficioCAAF <=0 ){
                    
                    $anagrafica->DATA_NASCITA = $anagraficaRappresentante->data_nasc;
                    $anagrafica->COMUNE_NASCITA = $anagraficaRappresentante->desc_comu_nasc;
                    $anagrafica->PROV_NASCITA = $anagraficaRappresentante->codi_sigl_prov_nasc;

                    $anagrafica->COMUNE_RESIDENZA = $anagraficaRappresentante->recapiti[0]['desc_geog_comu'];
                    $anagrafica->PROV_RESIDENZA = $anagraficaRappresentante->recapiti[0]['codi_geog_sigl_prov'];
                    $anagrafica->CAP_RESIDENZA = $anagraficaRappresentante->recapiti[0]['codi_geog_capp'];
                    $anagrafica->INDIRIZZO_RESIDENZA = $anagraficaRappresentante->recapiti[0]['desc_geog_strd'];    
                }
                $resAccount = $anagrafica->Save();
                
            }
            //Utils::print_array($resAccount);exit();
            
            
            if ($resAccount["esito"]) {
                $LoggedAccount->loadDati(); /* Load Dati correlati*/       
                $transaction = true;
            }
        } else {
            $transaction = true;
        }

        if ($transaction) {
//          
            session_name(SESSION_NAME);
            session_start();
            $_SESSION['ID'] = $LoggedAccount->ID;
            $_SESSION['AccountPermissions'] = array();
            if ($LoggedAccount->AMMINISTRATORE != SUPER_USER) {
                $perm = AccountPermessi::Load($LoggedAccount->ID_GRUPPO);
                foreach ($perm as $p) {                
                    $_SESSION['AccountPermissions'][$p['SOTTO_MODULO']]['READ'] = $p['READ'];
                    $_SESSION['AccountPermissions'][$p['SOTTO_MODULO']]['UPDATE'] = $p['UPDATE'];
                    $_SESSION['AccountPermissions'][$p['SOTTO_MODULO']]['DELETE'] = $p['DELETE'];
                    $_SESSION['AccountPermissions'][$p['SOTTO_MODULO']]['DISABLED'] = $p['DISABLED'];
                }
                
            }
            $LoggedAccount->AccountPermissions = $_SESSION['AccountPermissions'];
            $_SESSION['LoggedAccount'] = json_encode($LoggedAccount);     
            
        } else {
//            $return["esito"] = false;
//            $return["erroreDescrizione"] = "Al momento non è possibile completare l'operazione.";
        }
        return($LoggedAccount);
    }
    
    public static function loginQIAM($token = '') {
        global $con;
        
        $transaction = false;
        
        $return = Utils::getQIAM($token);
        
if(ROLE_CODE_FAKE)        
$return['dati']['data']['role_code'] = ROLE_CODE_FAKE; // TEST FAKE PER BYPASSARE IL GRUPPO DEI PERMESSI
        if($return['esito'] == 1){
            $role_code = intval($return['dati']['data']['role_code']);
            //$role_code = ROLE_CODE_LEGALE_RAPPRESENTANTE;
            $user = $return['dati']['data']['user'];            
            $vat_id = $return['dati']['data']['vat_id'];            
            $LoggedAccount = Account::load($user['fiscalCode']);
if(ROLE_CODE_FAKE)        
    $LoggedAccount->ID_GRUPPO = ROLE_CODE_FAKE; // TEST FAKE PER BYPASSARE IL GRUPPO DEI PERMESSI
            $LoggedAccount->ReturnQiam = $return['dati'];
            switch ($role_code) {
                case ROLE_CODE_LEGALE_RAPPRESENTANTE:
                    $rappresentanteLegale = new RappresentanteLegale();        
                    $anagraficaRappresentante = $rappresentanteLegale->GetAnagraficaSoggetto($user['fiscalCode']);
                    $anagraficaAzienda = new AnagraficaSoggetto();
                    $LoggedAccount->AziendaQiam = $anagraficaAzienda->GetPrgScheda($vat_id, true);

                    $LoggedAccount->CODICE_FISCALE = trim($user['fiscalCode']);
                    $LoggedAccount->ID_GRUPPO = GRUPPO_AZIENDE;
                    $LoggedAccount->AUTORIZZAZIONE_TRATTAMENTO = 1;
                    $resAccount = $LoggedAccount->Save();                           
                    if($resAccount['esito'] == 1){
                        $anagrafica = new AccountAnagrafica($LoggedAccount->CODICE_FISCALE);
                        $anagrafica->CODICE_FISCALE = $anagraficaRappresentante->codi_fisc;
                        $anagrafica->NOME = trim($user['name']);
                        $anagrafica->COGNOME = trim($user['lastName']);
                        $anagrafica->SESSO = trim($user['gender']);
                        $anagrafica->DATA_NASCITA = (trim($user['dateOfBirth'])!="" ? trim($user['dateOfBirth']) : $anagraficaRappresentante->data_nasc);
                        $anagrafica->COMUNE_NASCITA = (trim($user['placeOfBirth'])!="" ? trim($user['placeOfBirth']) : $anagraficaRappresentante->desc_comu_nasc );
                        $anagrafica->PROV_NASCITA = (trim($user['countryOfBirth'])!="" ? trim($user['countryOfBirth']) : $anagraficaRappresentante->codi_sigl_prov_nasc );
                        $anagrafica->COMUNE_RESIDENZA = $anagraficaRappresentante->recapiti[0]['desc_geog_comu'];
                        $anagrafica->PROV_RESIDENZA = $anagraficaRappresentante->recapiti[0]['codi_geog_sigl_prov'];
                        $anagrafica->CAP_RESIDENZA = $anagraficaRappresentante->recapiti[0]['codi_geog_capp'];
                        $anagrafica->INDIRIZZO_RESIDENZA = $anagraficaRappresentante->recapiti[0]['desc_geog_strd'];   
                        $anagrafica->SPID_CELLULARE = trim($user['mobilePhone']);
                        $anagrafica->SPID_EMAIL= trim($user['email']);
                        $resAccount = $anagrafica->Save();

                    }

                    if ($resAccount["esito"] == 1) {
                        $LoggedAccount->loadDati(); /* Load Dati correlati*/       
                        $transaction = true;
                    }
                        
                    
                    
                    break;
                case ROLE_CODE_CAA:
                    
                    $anagraficaAzienda = new AnagraficaSoggetto();
                    $LoggedAccount->AziendaQiam = $anagraficaAzienda->GetPrgScheda($vat_id, true);                    
                    $anagraficaRappresentante = $anagraficaAzienda->GetLegaleRappresententeFromPrgScheda($LoggedAccount->AziendaQiam['prg_scheda']);
                    
                    $LoggedAccount->CODICE_FISCALE = trim($user['fiscalCode']);
                    $LoggedAccount->ID_GRUPPO = GRUPPO_CAA;
                    $LoggedAccount->AUTORIZZAZIONE_TRATTAMENTO = 1;
                    $resAccount = $LoggedAccount->Save();                           
                    if($resAccount['esito'] == 1){
                        $anagrafica = new AccountAnagrafica($LoggedAccount->CODICE_FISCALE);
                        $anagrafica->CODICE_FISCALE = trim($user['fiscalCode']);
                        $anagrafica->NOME = trim($user['name']);
                        $anagrafica->COGNOME = trim($user['lastName']);
                        $anagrafica->SESSO = trim($user['gender']);
                        $anagrafica->INDIRIZZO_RESIDENZA = trim($user['address']);
                        $anagrafica->SPID_CELLULARE = trim($user['mobilePhone']);
                        $anagrafica->DATA_NASCITA = trim($user['dateOfBirth']);
                        $anagrafica->COMUNE_NASCITA = trim($user['placeOfBirth']);
                        $anagrafica->PROV_NASCITA = trim($user['countryOfBirth']);
                        $anagrafica->SPID_EMAIL= trim($user['email']);
                        $resAccount = $anagrafica->Save();
                    }

                    if ($resAccount["esito"] == 1) {
                        $LoggedAccount->loadDati(); /* Load Dati correlati*/       
                        $transaction = true;
                    }
                    
                    break;
                case ROLE_CODE_ADMIN:

                    $LoggedAccount->CODICE_FISCALE = trim($user['fiscalCode']);
                    $LoggedAccount->ID_GRUPPO = GRUPPO_AMMINISTRATORE;
                    $LoggedAccount->AUTORIZZAZIONE_TRATTAMENTO = 1;
                    $resAccount = $LoggedAccount->Save();                           
                    if($resAccount['esito'] == 1){
                        $anagrafica = new AccountAnagrafica($LoggedAccount->CODICE_FISCALE);
                        $anagrafica->CODICE_FISCALE = trim($user['fiscalCode']);
                        $anagrafica->NOME = trim($user['name']);
                        $anagrafica->COGNOME = trim($user['lastName']);
                        $anagrafica->SESSO = trim($user['gender']);
                        $anagrafica->INDIRIZZO_RESIDENZA = trim($user['address']);
                        $anagrafica->SPID_CELLULARE = trim($user['mobilePhone']);
                        $anagrafica->DATA_NASCITA = trim($user['dateOfBirth']);
                        $anagrafica->COMUNE_NASCITA = trim($user['placeOfBirth']);
                        $anagrafica->PROV_NASCITA = trim($user['countryOfBirth']);
                        $anagrafica->SPID_EMAIL= trim($user['email']);
                        $resAccount = $anagrafica->Save();
                    }

                    if ($resAccount["esito"] == 1) {
                        $LoggedAccount->loadDati(); /* Load Dati correlati*/       
                        $transaction = true;
                    }
                        
                    
                    
                    
                    break;                
            }
            
            
        }
        
        if ($transaction) {
//          
            session_name(SESSION_NAME);
            session_start();
            $_SESSION['ID'] = $LoggedAccount->ID;
            $_SESSION['AccountPermissions'] = array();
            if ($LoggedAccount->AMMINISTRATORE != SUPER_USER) {
                $perm = AccountPermessi::Load($LoggedAccount->ID_GRUPPO);
                foreach ($perm as $p) {                
                    $_SESSION['AccountPermissions'][$p['SOTTO_MODULO']]['READ'] = $p['READ'];
                    $_SESSION['AccountPermissions'][$p['SOTTO_MODULO']]['UPDATE'] = $p['UPDATE'];
                    $_SESSION['AccountPermissions'][$p['SOTTO_MODULO']]['DELETE'] = $p['DELETE'];
                    $_SESSION['AccountPermissions'][$p['SOTTO_MODULO']]['DISABLED'] = $p['DISABLED'];
                }
                
            }
            $LoggedAccount->AccountPermissions = $_SESSION['AccountPermissions'];
            $_SESSION['LoggedAccount'] = json_encode($LoggedAccount);     

            //Utils::print_array($_SESSION);exit();
            
        } else {
//            $return["esito"] = false;
//            $return["erroreDescrizione"] = "Al momento non è possibile completare l'operazione.";
        }
        return($LoggedAccount);
    }
    
    public static function loginFakeStressTest() {        
        $account = self::getFakeSPIDAccount();
        $LoggedAccount = Account::load($account->CODICE_FISCALE);
        if ($LoggedAccount->ID <= 0) {
            //$LoggedAccount->CODICE_FISCALE = $fiscalNumber;
            $resAccount = $LoggedAccount->Save();            
            $LoggedAccount->loadDati(); /* Load Dati correlati*/        
            if ($resAccount["esito"]) {
                $transaction = true;
            }
        } else {
            $transaction = true;
        }
        
        session_name(SESSION_NAME);
        session_start();
        $_SESSION['ID'] = $LoggedAccount->ID;
        $_SESSION['LoggedAccount'] = json_encode($LoggedAccount);   
        
        return($LoggedAccount);
    }

    public static function login() {
        global $con;
//        $return = array("esito" => false);
        $transaction = false;
        $LoggedAccount = new Account();
        $con->db_transactionStart();
        $params = self::checkLogLoginSpid();
        if ($params) {
            /* LOG ACCESSO */
            $logSpid = new SPID_ACCESSI();
            $logSpid->CODICE_FISCALE = $params["fiscalNumber"];
            $logSpid->INRESPONSETO = $params["inResponseTo"];
            $logSpid->AUTHNINSTANT = $params["AuthnInstant"];
            $resSpid = $logSpid->Save();
            /**/
            if ($resSpid["esito"] == 1) {
                $card = Utils::splitDocSPID($params["idCard"]);
                $LoggedAccount = Account::load($params["fiscalNumber"]);
                
//                $LoggedAccount->CODICE_FISCALE = $params["fiscalNumber"];
                /* SET Anagrafica SPID */
                if ($LoggedAccount->ID <= 0) {
                    $LoggedAccount->CODICE_FISCALE = $params["fiscalNumber"];                    
                    $resAccount = $LoggedAccount->Save();
                    $LoggedAccount->loadDati(); /* Load Dati correlati*/       
                    if ($resAccount["esito"]) {
                        $transaction = true;
                    }
                } else {
                    $transaction = true;
                }
            }
        }
        if ($transaction) {
//            $return["esito"] = true;
            $con->db_transactionCommit();
            if ($LoggedAccount->AUTORIZZAZIONE_TRATTAMENTO != 1) {
                $LoggedAccount->LoadDatiFromAnagraficaPersonale(); /* init anagrafica from anag personale */
                $LoggedAccount->Anagrafica->SPID_CELLULARE = $params["mobilePhone"];
                $LoggedAccount->Anagrafica->SPID_EMAIL = $params["email"];
                $LoggedAccount->Anagrafica->SPID_TIPO_DOCUMENTO = $card['TIPO_DOCUMENTO'];
                $LoggedAccount->Anagrafica->SPID_DOCUMENTO = $card['NUMERO_DOCUMENTO'];
                $LoggedAccount->Anagrafica->SPID_DOCUMENTO_ENTE = $card['ENTE_RILASCIO'];
                $LoggedAccount->Anagrafica->SPID_DOCUMENTO_RILASCIO = $card['DATA_EMISSIONE'];
                $LoggedAccount->Anagrafica->SPID_DOCUMENTO_SCADENZA = $card['DATA_SCADENZA'];
                //Utils::print_array($LoggedAccount);exit();
            }
            session_name(SESSION_NAME);
            session_start();
            $_SESSION['ID'] = $LoggedAccount->ID;
            $_SESSION['LoggedAccount'] = json_encode($LoggedAccount);
        } else {
//            $return["esito"] = false;
//            $return["erroreDescrizione"] = "Al momento non è possibile completare l'operazione.";
            $con->db_transactionRollback();
        }
        return($LoggedAccount);
    }

}

class SPID_ACCESSI extends DataClass {

//put your code here
    const TABLE_NAME = "SPID_ACCESSI";
    const SEQ_NAME = "SPID_ACCESSI_ID_SEQ";

    public $ID = 0; // Primary key
    public $CODICE_FISCALE = "";
    public $INRESPONSETO = "";
    public $AUTHNINSTANT = "";

    //public $DATA_ULTIMO_ACCESSO = null;


    public function __construct($src = null) {
        global $con;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['ID']) && $src['ID'] > 0) {
                $this->_loadAndFillObject($src['ID'], self::TABLE_NAME, $src);
            } else {
                $this->_loadByRow($src, $stripSlashes);
            }
        } elseif (intval($src)) {
            $this->_loadById($src, self::TABLE_NAME, true);
        }
    }

    public function Save() {
        global $con;
        $vars = get_object_vars($this);
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars);
        $queryabi = $con->prepare($sql);

        foreach ($vars as $k => $v) {
            if ($k == "ID" && $v == 0)
                continue;
            $queryabi->bindValue(":" . $k, $v);
        }
        try {
            $it = parent::_Save($queryabi);
        } catch (Exception $exc) {
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }

    /**
     * Funzione per la visualizzazione dei logs
     * @param type $record
     * @param type $campo
     */
    public function Parse(&$record, $campo = "") {
        $props = get_class_vars(get_class($this));
        array_push($record, array("Campo" => $campo, "Valore" => $this->$campo));
    }

}
