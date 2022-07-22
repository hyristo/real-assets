<?php

require_once "IDataClass.php";
/*
  /**
 * Description of UTENTI FIREBASE
 *
 * @author
 */

class UID_FIREBASE_ADMIN extends DataClass {

//put your code here
    const TABLE_NAME = "UID_FIREBASE_ADMIN";
    const SEQ_NAME = "UID_FIREBASE_ADMIN_ID_SEQ";

    public $ID = 0; // Primary key
    public $GUID = 0;
    public $EMAIL = "";
    public $NOME = "";
    public $COGNOME = "";
    public $SUPER_USER = 0; // 1 = SUPER UTENTE con permessi di creazione degli utenti
    public $GROUP_ID = 0;
    public $VERIFICATO = 0;
    
    public $AccountPermissions = array();
    public $ModulePermissions = array();
    public $Anagrafica = null;

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
        $this->loadDati();
    }

    public function loadDati() {
        $this->Anagrafica = $_SESSION['Anagrafica']; 
        $this->AccountPermissions = $_SESSION['AccountPermissions'];        
        
    }
    public function loadAccountAnagrafica() {
        $anagrafica= new AccountAnagrafica();
        $anagrafica->COGNOME = $this->COGNOME;
        $anagrafica->NOME = $this->NOME;
        return $anagrafica;
    }

    /*
     * controllo presenza codice Fiscale 
     */

    public function controlloEmail($email = "") {
        global $con;
        $return = array();
        $return['esito'] = 1;
        $return['descrizioneErrore'] = "";
        $verify = $con->prepare("SELECT  ID FROM " . self::TABLE_NAME . " WHERE EMAIL=:email ");
        $verify->bindParam(":email", $email);
        try {
            $verify->execute();
            $itVerify = $verify->fetch(PDO::FETCH_ASSOC);
            if (intval($itVerify['ID']) > 0) {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = "Indirizzo email già registrato";
            }
        } catch (Exception $exc) {
            $return['esito'] = -999;
            $return['erroreDescrizione'] = $exc->getMessage();
        }
        return $return;
    }

    /*
     * controllo presenza codice Fiscale 
     */

    public function controlloCodiceFiscale($codice_fiscale = "") {
        global $con;
        $return = array();
        $return['esito'] = 1;
        $return['descrizioneErrore'] = "";
        $verify = $con->prepare("SELECT  ID FROM " . self::TABLE_NAME . " WHERE CODICE_FISCALE=:codice_fiscale ");
        $verify->bindParam(":codice_fiscale", $codice_fiscale);
        try {
            $verify->execute();
            $itVerify = $verify->fetch(PDO::FETCH_ASSOC);
            if (intval($itVerify['ID']) > 0) {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = "Il codice fiscale dell'utente è già registrato";
            }
        } catch (Exception $exc) {
            $return['esito'] = -999;
            $return['erroreDescrizione'] = $exc->getMessage();
        }
        return $return;
    }

    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {

        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }

    /**
     * init account da dati di sessione
     */
    public static function initUidFirebaseFromSessione($account = null) {
        $loggedAccount = new UID_FIREBASE_ADMIN();
        if (!empty($account)) {
            $loggedAccount->_loadByRow($account);
        }
        return $loggedAccount;
    }

    /*
     * Load From Email (username)
     */

    public function LoadFromEmail($mail = "") {
        global $con;
        $uidFB = new UID_FIREBASE_ADMIN();
        $return = array('esito' => 1, 'descrizioneErrore' => '');
        $stmn = $con->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE EMAIL=:email ");
        $stmn->bindParam(":email", $mail);
        try {
            $stmn->execute();
            $row = $stmn->fetch(PDO::FETCH_ASSOC);
            if (intval($row['ID']) > 0) {
                Utils::FillObjectFromRow($uidFB, $row);
            }
        } catch (Exception $exc) {
            
        }
        return $uidFB;
    }

    public function Save() {
        global $con;        
        $vars = get_object_vars($this);
        unset($vars['AccountPermissions']);
        unset($vars['ModulePermissions']);
        unset($vars['Anagrafica']);
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
        $it['sql'] = $sql;
        return $it;
    }

    public function Delete() {
        return parent::_Delete(self::TABLE_NAME, "id = " . $this->id);
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function LogicalDelete() {
        return parent::_LogicalDelete(self::TABLE_NAME, "id = " . $this->id);
    }

    /**
     * Funzione per la visualizzazione dei logs
     * @param type $record
     * @param type $campo
     */
    public function Parse(&$record, $campo = "") {
        $props = get_class_vars(get_class($this));
        switch ($campo) {

            default:
                array_push($record, array("Campo" => $campo, "Valore" => $this->$campo));
                break;
        }
    }

    public function LogAccesso($email = "") {
        $action = "LOGIN";
        $logsBe = new LogsBe();
        $logsBe->ID_OPERATORE = $this->ID;
        $logsBe->IP = $_SERVER['REMOTE_ADDR'];
        $logsBe->OPERATION = "LOGIN UID_FIREBASE_ADMIN ";
        $logsBe->MODULE = "UID_FIREBASE_ADMIN";
        $logsBe->ACTION = $action;
        if ($this->ID > 0) {
            $logsBe->USERNAME = $this->EMAIL . " (" . $this->COGNOME . " " . $this->NOME . ")";
            $logsBe->OPERATION .= "(SUCCESS)";
        } else {
            $logsBe->USERNAME = $email;
            $logsBe->OPERATION .= "(FAILURE)";
        }
        $logsBe->INFO = json_encode($this);
        $logsBe->Save();
    }

    public static function Login($username = '', $password = '') {
        global $con;
        $response = Utils::initDefaultResponse();
        $loginFirebase = self::loginFirebase($_POST['email'], $_POST['password']);
        
        if ($loginFirebase['esito'] == 1) {
            $account = self::LoadFromEmail($_POST['email']);
            
            if ($account->VERIFICATO == 0) {
                //$response = self::CheckVerificaUserData($uid_firebase, $loginFirebase);                
                $record = self::getUserDataFirebase($loginFirebase['dati']['idToken']);
                if ($record['esito'] == 1) {
                    if ($record['dati']['users'][0]['emailVerified'] == 1) {
                        $account->VERIFICATO = 1;
                        $response = $account->Save();
                    } else {
                        $response['bottone'] = 1;
                        $response['token'] = $loginFirebase['dati']['idToken'];
                        $response['esito'] = -999;
                        $response['erroreDescrizione'] = "Verifica mail non riuscita! E' necessario confermare la creazione dell'account tramite il link contenuta nella mail ricevuta. Se non hai ricevuto la mail, clicca su Reinvia Mail per riceverne una nuova.";
                    }
                } else {
                    $response['esito'] = -999;
                    $response['erroreDescrizione'] = $loginFirebase['erroreDescrizione'];
                }
            } else {
                $response['esito'] = 1;
            }
            if ($response['esito'] == 1) {
                //SELF::SetSession($uid_firebase->CODICE_FISCALE);
                $LoggedAccount = $account; //new UID_FIREBASE_ADMIN($id_firebase['ID']);
                session_name(SESSION_NAME);
                session_start();
                
                $_SESSION['ID'] = $LoggedAccount->ID;

                $_SESSION['AccountPermissions'] = array();
                $_SESSION['Anagrafica'] = $LoggedAccount->loadAccountAnagrafica();
                if ($LoggedAccount->SUPER_USER != SUPER_USER) {
                    $perm = AccountPermessi::Load($LoggedAccount->GROUP_ID);
                    //Utils::print_array($perm);exit();
                    foreach ($perm as $p) {                
                        $_SESSION['AccountPermissions'][$p['SOTTO_MODULO']]['READ'] = $p['READ'];
                        $_SESSION['AccountPermissions'][$p['SOTTO_MODULO']]['UPDATE'] = $p['UPDATE'];
                        $_SESSION['AccountPermissions'][$p['SOTTO_MODULO']]['DELETE'] = $p['DELETE'];
                        $_SESSION['AccountPermissions'][$p['SOTTO_MODULO']]['DISABLED'] = $p['DISABLED'];
                    }
                    
                }
                $LoggedAccount->AccountPermissions = $_SESSION['AccountPermissions'];
                $LoggedAccount->Anagrafica = $_SESSION['Anagrafica'];
                $_SESSION['LoggedAccount'] = json_encode($LoggedAccount);
                
            }
        } else {
            $account = new UID_FIREBASE_ADMIN();
            $response['esito'] = -999;
            $response['erroreDescrizione'] = $loginFirebase['erroreDescrizione'];
        }
        try {
            $account->LogAccesso($_POST['email']); // LOG ACCESSO
        } catch (Exception $exc) {}
        return $response;
    }

    public static function verifyErroriGoogle($valore) {
        $messaggio = "";
        switch ($valore) {
            case "EMAIL_EXISTS":
                $messaggio = EMAIL_EXISTS;
                break;
            case "OPERATION_NOT_ALLOWED":
                $messaggio = OPERATION_NOT_ALLOWED;
                break;
            case "TOO_MANY_ATTEMPTS_TRY_LATER":
                $messaggio = TOO_MANY_ATTEMPTS_TRY_LATER;
                break;
            case "EMAIL_NOT_FOUND":
                $messaggio = EMAIL_NOT_FOUND;
                break;
            case "INVALID_PASSWORD":
                $messaggio = INVALID_PASSWORD;
                break;
            case "USER_DISABLED":
                $messaggio = USER_DISABLED;
                break;
            case "INVALID_ID_TOKEN":
                $messaggio = INVALID_ID_TOKEN;
                break;
            case "USER_NOT_FOUND":
                $messaggio = USER_NOT_FOUND;
                break;
            case "WEAK_PASSWORD":
                $messaggio = WEAK_PASSWORD;
                break;
        }
        return $messaggio; // Default will return
    }

    /*
     *
     * Registrazione Utente
     * 
     */

    public static function signUpFirebase($email = "", $password = "") {
        $return = array();
        $endpoint = LINK_FIREBASE . "accounts:signUp?key=" . API_FIREBASE_KEY;
        $postvars = json_encode(array(
            email => $email,
            password => $password,
            returnSecureToken => false
        ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        $result = curl_exec($ch);
        $arrayresponse = json_decode($result, true);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        if ($http_status == 200) {
            $return['esito'] = 1;
            $return['dati'] = $arrayresponse;
        } else {
            if (is_array($arrayresponse['error'])) {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = SELF::verifyErroriGoogle($arrayresponse['error']['message']);
            } else {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = "Servizio Momentaneamente Non Disponibile";
            }
        }
        curl_close($ch);
        return ($return);
    }

    /*
     *
     * Invio Mail di Verifica Utente 
     * 
     */

    public static function sendVerificationMailFirebase($idToken = "") {
        $return = array();
        $endpoint = LINK_FIREBASE . "accounts:sendOobCode?key=" . API_FIREBASE_KEY;
        $postvars = json_encode(array(
            idToken => $idToken,
            requestType => "VERIFY_EMAIL",
                //    returnSecureToken => true
        ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        $result = curl_exec($ch);
        $arrayresponse = json_decode($result, true);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);

//        print_r($result);

        if ($http_status == 200) {
            $return['esito'] = 1;
            $return['dati'] = $arrayresponse;
        } else {
            if (is_array($arrayresponse['error'])) {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = SELF::verifyErroriGoogle($arrayresponse['error']['message']);
            } else {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = "Servizio Momentaneamente Non Disponibile";
            }
        }
        curl_close($ch);
        return ($return);
    }

    /*
     *
     * Login Firebase
     * "e-mail"	------------------>L'email con cui l'utente sta effettuando l'accesso.
     * "password" -------------------->	La password per l'account.
     * "returnSecureToken" ------------------>	booleano	Se restituire o meno un ID e aggiornare il token. Dovrebbe essere sempre vero.
     */

    public static function loginFirebase($email = "", $password = "") {
        /*$check = Utils::checkLoginCredentials($email, $password);
        if ($check['esito'] <= 0) {
            return $check;
        }*/
        $return = array();
        $endpoint = LINK_FIREBASE . "accounts:signInWithPassword?key=" . API_FIREBASE_KEY;
        $postvars = json_encode(array(
            email => $email,
            password => $password,
            returnSecureToken => true
        ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        $result = curl_exec($ch);
        $arrayresponse = json_decode($result, true);

        //Utils::print_array($arrayresponse);

        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        if ($http_status == 200) {
            $return['esito'] = 1;
            $return['dati'] = $arrayresponse;
        } else {
            if (is_array($arrayresponse['error'])) {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = SELF::verifyErroriGoogle($arrayresponse['error']['message']);
            } else {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = "Servizio Momentaneamente Non Disponibile";
            }
        }
        curl_close($ch);
        return ($return);
    }

    /*
     *
     * Load Utente Firebase
     * IDToken	corda	Il token ID Firebase dell'account.
     * get user data
     */

    public static function loadUtenteFirebase($IDToken = "") {
        $return = array();
        $endpoint = LINK_FIREBASE . "accounts:lookup?key=" . API_FIREBASE_KEY;
        $postvars = json_encode(array(
            idToken => $IDToken,
        ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        $result = curl_exec($ch);
        $arrayresponse = json_decode($result, true);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        if ($http_status == 200) {
            $return['esito'] = 1;
            $return['dati'] = $arrayresponse;
        } else {
            //Utils::print_array($arrayresponse);
            if (is_array($arrayresponse['error'])) {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = SELF::verifyErroriGoogle($arrayresponse['error']['message']);
            } else {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = "Servizio Momentaneamente Non Disponibile";
            }
        }
        curl_close($ch);
        return ($return);
    }

    public static function deleteUtenteFirebase($IDToken = "") {
        $return = array();
        $endpoint = LINK_FIREBASE . "accounts:delete?key=" . API_FIREBASE_KEY;
        $postvars = json_encode(array(
            idToken => $IDToken,
        ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        $result = curl_exec($ch);
        $arrayresponse = json_decode($result, true);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        if ($http_status == 200) {
            $return['esito'] = 1;
            $return['dati'] = $arrayresponse;
        } else {
            if (is_array($arrayresponse['error'])) {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = SELF::verifyErroriGoogle($arrayresponse['error']['message']);
            } else {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = "Servizio Momentaneamente Non Disponibile";
            }
        }
        curl_close($ch);
        return ($return);
    }

    public static function getUserDataFirebase($idToken = "") {
        $return = array();
        $endpoint = LINK_FIREBASE . "accounts:lookup?key=" . API_FIREBASE_KEY;
        $postvars = json_encode(array(
            idToken => $idToken
        ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        $result = curl_exec($ch);
        $arrayresponse = json_decode($result, true);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        if ($http_status == 200) {
            $return['esito'] = 1;
            $return['dati'] = $arrayresponse;
        } else {
            if (is_array($arrayresponse['error'])) {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = SELF::verifyErroriGoogle($arrayresponse['error']['message']);
            } else {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = "Servizio Momentaneamente Non Disponibile";
            }
        }
        curl_close($ch);
        return ($return);
    }

    public static function sendPwdResetFirebase($email = "") {
        $return = array();
        $endpoint = LINK_FIREBASE . "accounts:sendOobCode?key=" . API_FIREBASE_KEY;
        $postvars = json_encode(array(
            requestType => "PASSWORD_RESET",
            email => $email,
        ));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        $result = curl_exec($ch);
        $arrayresponse = json_decode($result, true);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        if ($http_status == 200) {
            $return['esito'] = 1;
            $return['dati'] = $arrayresponse;
        } else {
//            Utils::print_array($arrayresponse);
            if (is_array($arrayresponse['error'])) {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = SELF::verifyErroriGoogle($arrayresponse['error']['message']);
            } else {
                $return['esito'] = -999;
                $return['erroreDescrizione'] = "Servizio Momentaneamente Non Disponibile";
            }
        }
        curl_close($ch);
        return ($return);
    }
    
    public function selectIdFromEmail($mail = "") {
        global $con;
        $return = array();
        $return['esito'] = 1;
        $return['descrizioneErrore'] = "";
        $verify = $con->prepare("SELECT ID FROM " . self::TABLE_NAME . " WHERE EMAIL=:email ");
        $verify->bindParam(":email", $mail);
        try {
            $verify->execute();
            $itVerify = $verify->fetch(PDO::FETCH_ASSOC);
            if (intval($itVerify['ID']) > 0) {
                $return['ID'] = $itVerify['ID'];
                $return['esito'] = 1;
            } else {
                $return['esito'] = -999;
            }
        } catch (Exception $exc) {
            $return['esito'] = -999;
            $return['descrizioneErrore'] = $exc->getMessage();
        }
        return $return;
    }


    /**
     * 
     * Verifica se l'utente ha il permesso di visualizzazione sul modulo specificato
     * @param $Module
     * @return bool risultato richiesta
     */
    public function HasModulesIstance($Module = "") {
        if ($Module != "") {
            $mi = Moduli::Count('"CODICE" = \'' . $Module . '\' AND "CANCELLATO" = 0');            
            if ($mi > 0)
                return true;
            return false;
        }
        return false;
    }

    /**
     * 
     * Verifica se l'utente ha il permesso di visualizzazione sul sotto modulo specificato
     * @param $Module
     * @param $SubModule
     * @return bool risultato richiesta
     */
    public function HasSubModulesIstance($Module = "", $SubModule = "") {
        if ($Module != "") {
            $sModule = SottoModuliRelation::Count('"MODULO" = \'' . $Module . '\' AND "SOTTO_MODULO" = \'' . $SubModule . '\' AND "DISABLED" = 0 ' );

            //echo $sModule;

            if ($sModule > 0)
                return true;
            return false;
        }
        return false;
    }


    /**
     * 
     * Verifica se l'utente ha il permesso di visualizzazione sul modulo specificato direttamente dall'account
     * @param $Module
     * @return bool risultato richiesta
     */
    public function HasModulesAccount($Module = "") {
        return array_key_exists($Module, $this->ModulePermissions);
    }
    
    /**
     * Restituisce TRUE se l'account e' un amministratore
     * 
     * @return bool
     */
    public function IsAmministratore() {
        return ($this->SUPER_USER == SUPER_USER);
    }

    /**
     * Restituisce TRUE se l'account ha il permesso specificato
     *
     * @param string $permission Il permesso da verificare
     * @return bool
     */
    public function HasPermission($array_module = array(), $permission = "DISABLED") {
        $return = false;
        if ($this->IsAmministratore()) {
            return true;
        }
        if ($permission != "") {
            if($permission == "DISABLED"){
                foreach ($array_module as $module) {
                    if (array_key_exists($module, $this->AccountPermissions)) {
                        if (!$this->AccountPermissions[$module][$permission]){
                            $return = true;                    
                        }
                    }
                }
                
            }else{
                foreach ($array_module as $module) {
                    if (array_key_exists($module, $this->AccountPermissions)) {
                        if ($this->AccountPermissions[$module][$permission]){
                            $return = true; 
                        }
                    }
                }
            }            
        }
        //echo $return;
        return $return;
    }

    /**
     * Restituisce l'oggetto Account_Gruppi associato
     *
     * @return object Account_Gruppi a cui e' associato l'utente o NULL
     *
     */
    public function GetGruppo() {
        if ($this->GROUP_ID > 0) {
            $gruppo = new AccountGruppi($this->GROUP_ID);
            if ($gruppo->ID > 0)
                return $gruppo;
        }
        return null;
    }
    
    public function GetNomeGruppo() {
        if ($grp = $this->GetGruppo())
            return $grp->NOME;
        return "";
    }

    /**
     * init account da dati di sessione
     */
    public static function initAccountFromSessione($account = null) {
        if (!empty($account)) {
            $response = new UID_FIREBASE_ADMIN();//$account['ID']                        
            $response->_loadByRow($account);            
        }
        return $response;
    }

    public function SwitchRoleCode() {
        $result = "";
        if ($this->ID >0){
            if($this->IsAmministratore()) {
                Utils::RedirectTo(HTTP_PRIVATE_SECTION . 'dashboard_admin.php');
            }else{
                Utils::RedirectTo(HTTP_PRIVATE_SECTION . 'dashboard.php');
            }
        }else{            
            Utils::RedirectTo(BASE_HTTP . 'logout.php');
        }
        return $result;
    }

}
