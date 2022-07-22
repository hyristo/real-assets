<?php

require_once "IDataClass.php";

/**
 * Description of Account
 *
 * @author Gigi
 */
class Account extends DataClass {

    const TABLE_NAME = "ACCOUNT";
    const SEQ_NAME = "ACCOUNT_ID_SEQ";

    public $ID = 0; // PK
    public $CODICE_FISCALE = '';
    public $ID_ENTE = 0;// DA DEFINIRE SE CONSIDERARE ID_ENTE o ID_UFFICIO della tabelle SINC_FA_AABRMAND_TAB 
    public $USERNAME = '';
    public $PASSWORD = '';
    public $AUTORIZZAZIONE_TRATTAMENTO = 0;
    public $CANCELLATO = 0;
    //public $CountDomande = 0;
    public $CanLogIn = false;
    public $ID_GRUPPO = 0;// 
    public $AMMINISTRATORE = 0;// 
    
    public $PrgSchedeAziende = array();
    public $AccountPermissions = array();
    public $ModulePermissions = array();
    public $ReturnQiam = array();
    public $AziendaQiam = array();

    /**/
//    public $PRIMO_ACCESSO = false;
    public $IP = null;
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
        $this->loadDati($src);
        
    }

    /*
     * Load dati a corredo dell'account (anagrafica, domande ecc.)
     */
    public function loadDati($src = array()) {
        
        $this->Anagrafica = $this->loadAccountAnagrafica();
        if($this->ID_ENTE<=0){
            $this->PrgSchedeAziende = $this->loadFascicoli(false);
        }
        //$this->CountDomande = $this->countDomandeAssociate();
        $this->AccountPermissions = $_SESSION['AccountPermissions'];
        $this->CanLogIn = $this->checkAuthPersonale();
        if (isset($src['IP']) && !empty($src['IP'])) {
            $this->IP = $src['IP'];
        } else {
            $this->IP = Utils::get_IP_address();
        }
    }
    
    public function countDomandeAssociate() {
        global $con;
        $response = 0;
        $filter = " CODICE_FISCALE = '".$this->CODICE_FISCALE."' AND STATO > ".DACOMPLETARE;
        
        $sql = "SELECT COUNT(ID) AS TOTREC FROM " . Domanda::TABLE_NAME . " WHERE " . $filter;        
        $stmn = $con->prepare($sql);
        try {
            $stmn->execute();
            $row = $stmn->fetch(PDO::FETCH_ASSOC);
            $response = $row['TOTREC'];
        } catch (Exception $exc) {

        }
        
        return $response;
    }
    /**
     * Controllo se sono un utente presente all'interno dell'anagrafica fornita dalla regione 
     * @global type $con
     * @return type boolean
     */
    public function checkAuthPersonale() {
        global $con;
        $response = 1;
        return $response;
        /*
        $filter = " CODICE_FISCALE = '".$this->CODICE_FISCALE."' AND CANCELLATO = 0 ";
        
        if(CONTROLLO_ANZIANITA_POSIZIONE){
            $filter.=" AND ((TO_DATE('".DATA_FINE_CALCOLO_ESPERIENZA."','dd/mm/YYYY hh24:mi:ss')-ANZIANITA_POSIZIONE)/30) >=".MIN_ANZIANITA_POSIZIONE;
        }
        
        $sql = "SELECT COUNT(CODICE_FISCALE) AS TOTREC FROM " . AnagraficaPersonale::TABLE_NAME . " WHERE " . $filter;        
        $stmn = $con->prepare($sql);
        try {
            $stmn->execute();
            $row = $stmn->fetch(PDO::FETCH_ASSOC);
            $response = intval($row['TOTREC']);
        } catch (Exception $exc) {

        }
        
        return $response;*/
    }
    
    /**
     * Load dati from anagrafica personale (primo accesso o accesso con anagrafica non confermata)
     */
    public function LoadDatiFromAnagraficaPersonale(){
        $anag = AnagraficaPersonale::load($this->CODICE_FISCALE);
        $this->Anagrafica->NOME = $anag->NOME;
        $this->Anagrafica->COGNOME = $anag->COGNOME;
        $this->Anagrafica->DATA_NASCITA = $anag->DATA_NASCITA;
        $this->Anagrafica->SESSO = Utils::extractGenderFromCF($this->CODICE_FISCALE);        
    }

    /**
     * init account da dati di sessione
     */
    public static function initAccountFromSessione($account = null) {
        if (!empty($account)) {
            $response = new Account();//$account['ID']            
            $anagrafica = new AccountAnagrafica();
            $anagrafica->_loadByRow($account['Anagrafica']);
//            $account['Anagrafica'] = $anagrafica; // assegnamo un oggetto come anagrafica e non un array
            $response->_loadByRow($account);
            //$response->CountDomande = $response->countDomandeAssociate();
            $response->CanLogIn = $response->checkAuthPersonale();
            $response->Anagrafica = $anagrafica; // assegnamo un oggetto come anagrafica e non un array
            if ($response->AUTORIZZAZIONE_TRATTAMENTO != 1) {
                $response->Anagrafica->SPID_CELLULARE = $account['Anagrafica']['SPID_CELLULARE'];
                $response->Anagrafica->SPID_EMAIL = $account['Anagrafica']['SPID_EMAIL'];
                $response->Anagrafica->SPID_TIPO_DOCUMENTO = $account['Anagrafica']['SPID_TIPO_DOCUMENTO'];
                $response->Anagrafica->SPID_DOCUMENTO = $account['Anagrafica']['SPID_DOCUMENTO'];
                $response->Anagrafica->SPID_DOCUMENTO_ENTE = $account['Anagrafica']['SPID_DOCUMENTO_ENTE'];
                $response->Anagrafica->SPID_DOCUMENTO_RILASCIO = $account['Anagrafica']['SPID_DOCUMENTO_RILASCIO'];
                $response->Anagrafica->SPID_DOCUMENTO_SCADENZA = $account['Anagrafica']['SPID_DOCUMENTO_SCADENZA'];
            }
        }
        return $response;
    }

    public function loadAccountAnagrafica() {
        return new AccountAnagrafica($this->CODICE_FISCALE);
    }
    
    public function loadFascicoli($soggetto = true) {
        
        $res = array();
        if($this->ID_ENTE>0){
            $res = AnagraficaUffici::loadMandatiFromUfficio(true);
        }else{
            $rappresentanteLegale = new RappresentanteLegale();
            $res = $rappresentanteLegale->loadAziendeCollegateFromCodiceFiscale($this->CODICE_FISCALE, $soggetto);
        }
        return $res;
    }
 
    /*
     *  Set Session with current LoggedAccount Load By Codice Fiscale
     */

    public function reloadSession() {
        global $LoggedAccount;
        session_name(SESSION_NAME);
        session_start();
        $LoggedAccount = new Account($LoggedAccount->ID);
        $_SESSION['ID'] = $LoggedAccount->ID;
        $_SESSION['LoggedAccount'] = json_encode($LoggedAccount);        
        //$_SESSION['PrgSchedeAziende'] = json_encode($this->loadFascicoli());
    }

    /*
     *  Set Session with current LoggedAccount Load By Codice Fiscale
     */

    public function SetSession($codiceFiscale = '') {
        session_name(SESSION_NAME);
        session_start();
        $LoggedAccount = SELF::load($codiceFiscale);
        $_SESSION['ID'] = $LoggedAccount->ID;
        $_SESSION['LoggedAccount'] = json_encode($LoggedAccount);
    }

    /*
     * Select id by Field 
     * default field: codice Fiscale
     */

    public static function load($field = "", $fieldName = "CODICE_FISCALE") {
        global $con;
        $account = new Account();
        if (!empty($field) && !empty($fieldName)) {
            $stmn = $con->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE " . $fieldName . " =:" . $fieldName . " ");
            $stmn->bindParam(":" . $fieldName, $field);
            try {
                $stmn->execute();
                $row = $stmn->fetch(PDO::FETCH_ASSOC);
                
                if (intval($row['ID']) > 0) {
                    Utils::FillObjectFromRow($account, $row);
                    $account->loadDati(); // DA VERIFICARE 
                }
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $account;
    }

    public function Save() {
        global $con, $LoggedAccount;
        $vars = get_object_vars($this);
        /* REMOVE LOCAL VAR */
        unset($vars['IP']);
        unset($vars['Anagrafica']);        
        unset($vars['CanLogIn']);
        unset($vars['PrgSchedeAziende']);
        unset($vars['AccountPermissions']);
        unset($vars['ModulePermissions']);
        unset($vars['ReturnQiam']);
        unset($vars['AziendaQiam']);
        /**/
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars);
        $queryabi = $con->prepare($sql);
        foreach ($vars as $k => $v) {
            if ($k == "ID" && $v == 0)
                continue;
            $queryabi->bindValue(":" . $k, $v);
            //echo $k." => ". $v."<br>";
        }
        
        try {
            $it = parent::_Save($queryabi);
        } catch (Exception $exc) {
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function Delete() {
        return parent::_LogicalDelete(self::TABLE_NAME, "id = " . $this->id);
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

    public function SaveAuth($auth = null) {
        if ($this->AUTORIZZAZIONE_TRATTAMENTO == 1) {
            $response = Utils::initDefaultResponse(true);
        } else {
            $response = Utils::initDefaultResponse(false, "L'autorizzazione al trattamento dei dati è obbligatoria");
            if (!empty($auth)) {
                $this->AUTORIZZAZIONE_TRATTAMENTO = 1;
                $response = $this->Save();
            }
        }
        return $response;
    }

    public function getFieldAuthConsenso() {
        global $LoggedAccount;
        $reponse = '';
        if ($LoggedAccount->AUTORIZZAZIONE_TRATTAMENTO > 0) {
            $reponse = '<div class="col-12 text-right">Visualizza <a href="privacy.php" target="_blank"><b>l\'informativa sulla privacy</b></a> ai sensi del Regolamento UE 2016/679 (GDPR)</div>';
        } else {
            $reponse = '
            <div class="col-2 text-right">
                <input class="form-control " type="checkbox" id="consenso_informativo" name="AUTORIZZAZIONE_TRATTAMENTO" onchange="changeView()" >
            </div>
            <div class="col-10">
            <label for="defaultCheck1">
                <span class="text-danger">*&nbsp;</span>Autorizzo il trattamento dei dati forniti ai sensi delle vigenti disposizioni in materia di <a href="privacy.php" target="_blank"><b>privacy</b></a> ai sensi del Regolamento UE 2016/679 (GDPR)
            </label></div>';
        }
        return $reponse;
    }

    public function checkAuthShowDomanda($idDomanda = 0) {
        global $LoggedAccount;
        $record = null;
        if (intval($idDomanda) > 0) {
            //$record = new Domanda($idDomanda);
            $record = Domanda::loadDomandaCompleta($idDomanda);
            if ($record['CODICE_FISCALE'] != $LoggedAccount->CODICE_FISCALE) {            
                Utils::RedirectTo('dashboard.php');
            }
        } else {
            Utils::RedirectTo('dashboard.php');
        }
        return $record;
    }

    public function checkAuthShowDomandaPage($idDomanda = 0) {
        global $LoggedAccount;
        if (intval($idDomanda) > 0) {
            $domanda = new Domanda($idDomanda);
            if ($domanda->CODICE_FISCALE != $LoggedAccount->CODICE_FISCALE) {
                Utils::RedirectTo(HTTP_PRIVATE_SECTION.'dashboard.php');
            }
        } else {
            Utils::RedirectTo(HTTP_PRIVATE_SECTION.'dashboard.php');
        }
    }

    public function checkAuthShowAziendaPage($prg_scheda = -999) {
        global $LoggedAccount;
        
        if($prg_scheda == -999){
            
            if($LoggedAccount->ID_ENTE > 0){
                Utils::RedirectTo(HTTP_PRIVATE_SECTION.'dashboard.php');
            }else{
                $prg = AnagraficaSoggetto::GetPrgScheda();                
                if($prg['prg_scheda']<=0){
                    Utils::RedirectTo(HTTP_PRIVATE_SECTION.'no_access.php');
                }
            }
        }else{            
            
            if($LoggedAccount->ID_ENTE > 0){
                $array_mandati = AnagraficaUffici::loadMandatiFromUfficio(false);
                //Utils::print_array($array_mandati);exit();
                if(!in_array($prg_scheda, $array_mandati)){
                    Utils::RedirectTo(HTTP_PRIVATE_SECTION.'dashboard.php');                    
                }
            }else{
                
                $array_collegate = RappresentanteLegale::loadAziendeCollegateFromCodiceFiscale(null, false);                
                
                $prg = AnagraficaSoggetto::GetPrgScheda();
                if(!in_array($prg_scheda, $array_collegate) && $prg['prg_scheda'] != $prg_scheda){
                    Utils::RedirectTo(HTTP_PRIVATE_SECTION.'no_access.php?uu');                                        
                }
            }
            
        }
        
    }
    
    /**
     * Verifica se si è autorizzati a visionare i dati della scheda
     * @global type $LoggedAccount
     * @param type $prg_scheda
     * @return boolean
     */
    public function checkAuthorized($prg_scheda = 0) {
        global $LoggedAccount;        
        if (intval($prg_scheda) > 0) {            
            if($LoggedAccount->ID_ENTE > 0){
                $array_mandati = AnagraficaUffici::loadMandatiFromUfficio(false);
                //Utils::print_array($array_mandati);
                if(!in_array($prg_scheda, $array_mandati)){
                    $response = true;
                } else {
                    $response = false;
                }
            }else{
                $array_collegate = RappresentanteLegale::loadAziendeCollegateFromCodiceFiscale(null, false);                
                $prg = AnagraficaSoggetto::GetPrgScheda();
                if(!in_array($prg_scheda, $array_collegate) && $prg['prg_scheda'] != $prg_scheda){
                    $response = false;
                }else{
                    $response = true;
                }
            }
            
        } else {
            if($LoggedAccount->ID_ENTE > 0){
                $response = true;
            }else{
                $prg = AnagraficaSoggetto::GetPrgScheda();
                if($prg['prg_scheda']<=0){
                    $response = false;
                }else{
                    $response = true;
                }
            }
        }
        return $response;
    }
    
    

    public function checkTrattamento($trattamento = null) {
        global $LoggedAccount;
        $response = false;
        if ($LoggedAccount->AUTORIZZAZIONE_TRATTAMENTO == 1) {
            $response = true;
        } else if (strtolower($trattamento) == 'on' || intval($trattamento) == 1) {
            $response = true;
        }
        return $response;
    }

    public function checkAuthNewAccount() {
                
        $response = false;
        if (Utils::isStatoSportelloPresentazione()) {
            $response = true;
        }
        else if (Utils::isStatoSportelloPresentazione(true) && !empty($this->Anagrafica->CODICE_FISCALE)) {
            $response = true;
        }        
        return $response;
    }

    public function checkAuthApiFase() {        
        return $this->checkAuthNewAccount();
    }

    public function checkAuthPageFase() {
//        global $LoggedAccount;
        if (!$this->checkAuthNewAccount()) {
            Utils::RedirectTo(HTTP_PRIVATE_SECTION . 'info.php');
        } /*else {
            if (empty($this->Anagrafica->CODICE_FISCALE) && (
                    Utils::checkCurrentPage('dashboard') || Utils::checkCurrentPage('faq') || Utils::checkCurrentPage('domanda')
                    )
            ) {
                Utils::RedirectTo(HTTP_PRIVATE_SECTION . 'account.php');
            }
        }*/
    }

//    public function checkCFDelega($input) {
//        if ($input["DICHIARAZIONE_DELEGA"] && strtoupper($input["CODICE_FISCALE"]) == strtoupper($input["CODICE_FISCALE_DIRIGENTE"])) {
//            return false;
//        }
//        if (!$input["DICHIARAZIONE_DELEGA"] && strtoupper($input["CODICE_FISCALE"]) != strtoupper($input["CODICE_FISCALE_DIRIGENTE"])) {
//            return false;
//        }
//        return true;
//    }

    public function checkStateEditableFieldPage() {
        $response = Utils::getFieldsState(Utils::isStatoSportelloPresentazione());
        //Utils::print_array($this);
        /*if ($this->CountDomande > 0) {
            $response = array(
                'readonly' => "readonly='readonly'",
                'hidden' => "hidden='true'",
                'disabled' => "disabled='true'",
                'stato' => 0
            );
        }*/
        return $response;
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
        return ($this->AMMINISTRATORE == SUPER_USER);
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
        if ($this->ID_GRUPPO > 0) {
            $gruppo = new AccountGruppi($this->ID_GRUPPO);
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
     * Restituisce il ruolo inserito dall QIAM
     * @return type
     */
    public function GetRoleCode() {        
        return intval($this->ReturnQiam['data']['role_code']);
    }
    
    /**
     * Gestisce il redirect delle pagine in base al ruolo
     */
    public function SwitchRoleCode() {
        $result = "";
        if ($this->ID >0){
            switch ($this->GetRoleCode()) {
                case ROLE_CODE_LEGALE_RAPPRESENTANTE:                                                
                case ROLE_CODE_CAA:
                    Utils::RedirectTo(HTTP_PRIVATE_SECTION . 'dashboard.php');
                    break;                                            
                case ROLE_CODE_ADMIN:
                    Utils::RedirectTo(HTTP_PRIVATE_SECTION . 'dashboard_admin.php');
                    break;  
                default:
                    $result = "<center><h1>IL CODICE RUOLO ".$this->GetRoleCode()." NON E' CENSITO PER QUESTO APPLICATIVO</h1></center>";
                    //Utils::RedirectTo(BASE_HTTP . 'logout.php');
                    break;
            }
        }else{
            $result =  "<center><h1>ACCESSO NON CONSENTITO</h1></center>";
            //Utils::RedirectTo(BASE_HTTP . 'logout.php');
        }
        return $result;
    }
    
    
}
