<?php

require_once "IDataClass.php";

/**
 * Description of AccountAnagrafica
 *
 * @author Gigi
 */
class AccountAnagrafica extends DataClass {

    const TABLE_NAME = "ACCOUNT_ANAGRAFICA";    
    const SEQ_NAME = "ACCOUNT_ANAG_ID_SEQ";

    public $ID = "";
    public $CODICE_FISCALE = "";
    public $NOME = "";
    public $COGNOME = "";
    public $DATA_NASCITA = null;
    public $COMUNE_NASCITA = "";
    public $PROV_NASCITA = "";
    public $CAP_NASCITA = "";
    public $COMUNE_RESIDENZA = "";
    public $PROV_RESIDENZA = "";
    public $CAP_RESIDENZA = "";
    public $INDIRIZZO_RESIDENZA = "";
    public $CIVICO_RESIDENZA = "";
    public $SPID_CELLULARE = "";
    public $SPID_DOCUMENTO = "";
    public $SPID_DOCUMENTO_SCADENZA = "";
    public $SPID_DOCUMENTO_RILASCIO = "";
    public $SPID_TIPO_DOCUMENTO = "";
    public $SPID_DOCUMENTO_ENTE = "";
    public $SPID_EMAIL = "";
    public $DATA_MODIFICA = null;
    public $DATA_REGISTRAZIONE = null;
    public $RECAPITO_ALTERNATIVO = "";
//    public $DICHIARAZIONE_DELEGA = 0;
//    public $CODICE_FISCALE_DIRIGENTE = "";
    public $SESSO = "";
//    public $FORZATURA_CODICE_FISCALE = "";
    //public $DIPARTIMENTO_REGIONALE = "";
    
    public function __construct($src = null) {
        global $con;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['CODICE_FISCALE']) && $src['CODICE_FISCALE'] != "") {
                $this->_loadAndFillObject($src['CODICE_FISCALE'], self::TABLE_NAME, $src, null, "CODICE_FISCALE");
            } else {
                $this->_loadByRow($src, $stripSlashes);
            }
        } elseif ($src) {
            $this->_loadById($src, self::TABLE_NAME, true, null, "CODICE_FISCALE");
        }
    }

    /**
     * Save
     */
    public function Save() {
        global $con, $LoggedAccount;
        $response = array();
        //$this->DATA_NASCITA = date('Y-m-d');
        $this->DATA_MODIFICA = date('Y-m-d');
        $this->DATA_REGISTRAZIONE = date('Y-m-d');
        $vars = get_object_vars($this);
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars, "ID");
        //echo $sql."\n";
        $queryabi = $con->prepare($sql);
        foreach ($vars as $k => $v) {
            if ($k == "ID" && empty($v))
                continue;
            $queryabi->bindValue(":" . $k, $v);
            //echo " ".$k.", ".$v."\n";
        }
        try {
            $response = parent::_Save($queryabi);
        } catch (Exception $exc) {
            $response['descrizioneErrore'] = $exc->getMessage();
        }
        return $response;
    }

    /**
     * Load From Fields (1 or 2 fields) 
     * Default filter Codice AND Seriale
     * return empty object without filter
     */
//    public static function load($field1Value = '', $field1 = 'CODICE', $field2Value = '', $field2 = 'SERIALE') {
//        global $con, $LoggedAccount;
//        $istituto = new AccountIstituto();
//        $filter = "";
//        if (!empty($field1Value)) {
//            $filter .= ($filter != "" ? " AND " : "") . $field1Value . " = :field1";
//        }
//        if (!empty($field2Value)) {
//            $filter .= ($filter != "" ? " AND " : "") . $field2Value . " = :field2";
//        }
//        if (!empty($filter)) {
//            $stmn = $con->prepare("SELECT * FROM " . self::TABLE_NAME . " WHERE " . $filter); //CODICE = :codice AND SERIALE=:seriale
//            $stmn->bindParam(":codice", $codice);
//            $stmn->bindParam(":seriale", $seriale);
//            try {
//                $stmn->execute();
//                $row = $stmn->fetch(PDO::FETCH_ASSOC);
//                if (!empty($row['ACCOUNT'])) {
//                    Utils::FillObjectFromRow($istituto, $row);
//                }
//            } catch (Exception $exc) {
//                
//            }
//        }
//        return $istituto;
//    }

    /**
     * Funzione per il load dei dati per DataTable
     *
     * @return array Risultato booleano dell'operazione
     */
    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }

    /**
     * Elimina l'oggetto corrente, in modo logico, dal database e restituisce TRUE se ha successo
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

    /**
     *  Funzione per il controllo se i dati SPID presenti nel LoggedAccount (ricevuti da SPID) sono uguali a quelli che vengono salvati per evitare manimissioni 
     */
    public static function verificaDatiSPID($input = array()) {
        global $LoggedAccount;
        $response = false;
        if (!empty($input)) {
            if (
                isset($input['CODICE_FISCALE']) && $LoggedAccount->CODICE_FISCALE == $input['CODICE_FISCALE'] &&
                isset($input['SPID_CELLULARE']) && $LoggedAccount->Anagrafica->SPID_CELLULARE == $input['SPID_CELLULARE'] &&
                isset($input['SPID_EMAIL']) && $LoggedAccount->Anagrafica->SPID_EMAIL == $input['SPID_EMAIL'] &&                    
                isset($input['SPID_DOCUMENTO']) && $LoggedAccount->Anagrafica->SPID_DOCUMENTO == $input['SPID_DOCUMENTO'] &&
                isset($input['SPID_DOCUMENTO_SCADENZA']) && $LoggedAccount->Anagrafica->SPID_DOCUMENTO_SCADENZA == $input['SPID_DOCUMENTO_SCADENZA'] &&
                isset($input['SPID_DOCUMENTO_RILASCIO']) && $LoggedAccount->Anagrafica->SPID_DOCUMENTO_RILASCIO == $input['SPID_DOCUMENTO_RILASCIO'] &&
                isset($input['SPID_TIPO_DOCUMENTO']) && $LoggedAccount->Anagrafica->SPID_TIPO_DOCUMENTO == $input['SPID_TIPO_DOCUMENTO'] &&
                isset($input['SPID_DOCUMENTO_ENTE']) && $LoggedAccount->Anagrafica->SPID_DOCUMENTO_ENTE == $input['SPID_DOCUMENTO_ENTE']
            ) {
                $response = true;
            }
        }
        return $response;
    }

    public function checkValidazioneCodiceFiscale($codiceFiscale = ''){
                
        if($this->FORZATURA_CODICE_FISCALE == 0){
            $cf = new CodiceFiscale();            
            $data = Date::FormatDate($this->DATA_NASCITA);
            $codice = $cf->calcola($this->NOME, $this->COGNOME, $data, $this->SESSO, $this->COMUNE_NASCITA, $this->PROV_NASCITA);     
            if($codiceFiscale != $codice || $codiceFiscale == ''){
                return false;
            }
        }
        return true;
    }
    
}
