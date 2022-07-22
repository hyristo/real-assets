<?php

/**
 * Description of Application
 *
 * @author Gigi
 */
class Application extends DataClass {

    const SUCCESS = 1; // controlli ok
    const ACCESS_ERR = -1; // errore di accesso (lato WEB => response con messaggio e codice errore in json)
    const ACCESS_WS_ERR = -2; // errore di accesso (lato WS => response con exit(0))

    private $account = null;
    private $fascicoli = array();
    private $con = null;
    private $conSpid = null;
    private $conSian = null;
    private $conSafe = null;
    public $lastEsito = 0;
    public $lastMessageError = '';

    public function __construct() {
        return $this->_initApp();
    }
    
    public function getAccount(){
        return $this->account;
    }
    
    public function getCon(){
        return $this->con;
    }
    
    public function getConSpid(){
        return $this->conSpid;
    }
    
    public function getConSian(){
        return $this->conSian;
    }
    public function getConSafe(){
        return $this->conSafe;
    }
    /* CHECK IP ACCESS */
    private function _checkIP() {
        $response = false;
        if (ENABLE_CHECK_IP) {
            if (!Utils::access_IP_control() && !Utils::checkCurrentPage('index')) {
                if (Utils::checkCurrentPage('_webservice')) {
                    $this->lastEsito = self::ACCESS_ERR;
                    $this->lastMessageError = 'Accesso scaduto: si prega di aggiornare la pagina ed effettuare il login al sistema.';                    
                } else {
                    Utils::RedirectTo(BASE_HTTP . "logout.php");
                    exit(0);
                }
            } else {
                $response = true;
            }
        } else {
            $response = true;
        }
        return $response;
    }

    /* SET DB CONNECTION  */
    private function _checkConnectionDB() {
        global $con, $conSpid,$conSian, $conSafe;
        if (!isset($con)) {
            //$connessione = new Database(DBMS, DB_SERVER, array('DB_USER' => DB_USER, 'DB_PASS' => DB_PASS));
            $connessione = new Database(DBMS, DB_NAME, array('DB_SERVER' => DB_SERVER, 'DB_USER' => DB_USER, 'DB_PASS' => DB_PASS));
            $this->con = $connessione->db;
        } else {
            $this->con = $con;
        }

        if (!isset($conSpid) && DBMS_SPID) {
            //$connessioneSpid = new Database(DBMS, DB_SERVER, array('DB_USER' => DB_USER_SPID, 'DB_PASS' => DB_PASS_SPID));
            $connessioneSpid = new Database(DBMS_SPID, DB_NAME_SPID, array('DB_SERVER' => DB_SERVER_SPID, 'DB_USER' => DB_USER_SPID, 'DB_PASS' => DB_PASS_SPID));
            $this->conSpid = $connessioneSpid->db;
        } else {
            $this->conSpid = $conSpid;
        }
        if (!isset($conSian) && DBMS_SIAN) {
            //$connessioneSpid = new Database(DBMS, DB_SERVER, array('DB_USER' => DB_USER_SPID, 'DB_PASS' => DB_PASS_SPID));
            $connessioneSian = new Database(DBMS_SIAN, DB_NAME_SIAN, array('DB_SERVER' => DB_SERVER_SIAN, 'DB_USER' => DB_USER_SIAN, 'DB_PASS' => DB_PASS_SIAN));
            $this->conSian = $connessioneSian->db;
        } else {
            $this->conSian = $conSian;
        }
        
        if (!isset($conSafe) && DBMS_SAFE) {
            $connessioneSafe = new Database(DBMS_SAFE, DB_NAME_SAFE, array('DB_SERVER' => DB_SERVER_SAFE, 'DB_USER' => DB_USER_SAFE, 'DB_PASS' => DB_PASS_SAFE));
            $this->conSafe = $connessioneSafe->db;
        } else {
            $this->conSafe = $conSafe;
        }
        
        
    }
    
    private function _checkModuleAction(){
        global $moduliActionAccessNoControll;
        $response = false;
        if (isset($_REQUEST["module"]) && isset($_REQUEST["action"])){
            if (isset($moduliActionAccessNoControll[$_REQUEST["module"]])){
                if (in_array($_REQUEST["action"], $moduliActionAccessNoControll[$_REQUEST["module"]])){
                    $response = true;
                }
            }
        }
        return $response;
    }

    private function _initApp() {
        global $pageAccessNoControll, $moduliActionAccessNoControll, $currentPageAccess;
        $response = Utils::initDefaultResponse();
        if ($this->_checkIP()) {
            session_start([
                    'cookie_lifetime' => COOKIE_LIFETIME
                    //'gc_maxlifetime' => 120//GC_MAXLIFETIME
            ]);
            
            if (isset($_SESSION['ID']) && intval($_SESSION['ID']) > 0) {
                if (isset($_SESSION['LoggedAccount'])) {
                    $this->lastEsito = self::SUCCESS;
                    $this->account = UID_FIREBASE_ADMIN::initAccountFromSessione(json_decode($_SESSION['LoggedAccount'], true));                    
                } else {
                    $this->lastEsito = self::SUCCESS;
                    $this->account = new UID_FIREBASE_ADMIN(intval($_SESSION['ID']));
                }
                $currentPageAccess = Utils::getCurrentPageCalled();
                /* CONTROLLO SE NEL PERIODO DI ACCESSO HO I REQUISITI PER ACCEDERE AL SISTEMA */
                if (Utils::checkCurrentPage('_webservice')) {
                    if (!in_array($currentPageAccess, $pageAccessNoControll)) {                        
                        $this->lastEsito = self::ACCESS_WS_ERR;
                    }
                } else if (!Utils::checkCurrentPage('info')) {
                    $this->lastEsito = self::SUCCESS;                    
                }
                
                //Utils::print_array($this);//exit('-------///');
                
            } else {
                $this->lastEsito = self::SUCCESS;
                $this->account = new UID_FIREBASE_ADMIN();
                $currentPageAccess = Utils::getCurrentPageCalled();
                /* echo $currentPageAccess;
                echo "<br>";
                echo $_SERVER['SCRIPT_FILENAME'];
                echo "<br>";
                echo "entro prima"; */
                if (Utils::checkCurrentPage('_webservice') && (!in_array($currentPageAccess, $pageAccessNoControll) || !$this->_checkModuleAction())) {
                    $this->lastEsito = self::ACCESS_ERR;
                    $this->lastMessageError = 'Accesso scaduto: si prega di aggiornare la pagina ed effettuare il login al sistema.';
                    // echo "entro";
                } else if (!in_array($currentPageAccess, $pageAccessNoControll)) {
                    // echo "entro2";
                    Utils::RedirectTo(BASE_HTTP . "login.php");
                    exit(0);
                }
            }
        }
        
        if ($this->lastEsito == self::SUCCESS){
            $this->_checkConnectionDB();
        }
    }
    
    private function _loadModules() {
        global $LoggedAccount;
        $AppModules = array();        
        $AppModules["MAGAZZINO"] = new Module("Magazzino", null, "MAG");
        
        
        
    }    

}

?>