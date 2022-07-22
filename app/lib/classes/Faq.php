<?php

require_once "IDataClass.php";

/**
 * Description of Faq
 *
 * @author Anselmo
 */
class Faq extends DataClass {

    const TABLE_NAME = "FAQ";
    const SEQ_NAME = "FAQ_ID_SEQ";
    const SP_ = "SPFAQSAVE";

    public $ID = 0; // Primary key    
    public $ID_UID_FIREBASE_ADMIN = 0;
    public $OGGETTO = "";
    public $DOMANDA = "";
    public $PUBBLICATA = 0;
    public $CANCELLATO = 0;
    public $DATA_INSERIMENTO = null;
    public $ID_CATEGORIA = 0;

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

            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE ID = :id";
            $query = $con->prepare($sql);
            $query->bindParam(":id", $src);
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
//                $it[0]['DOMANDA'] = html_entity_decode(htmlspecialchars_decode(stream_get_contents($it[0]['DOMANDA'])));
                Utils::FillObjectFromRow($this, $it[0]);
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }
    }

    /**
     * Restituisce il totale i
     *
     * @param bool $filter Se specificato, filtra i comune in funzione dei parametri specificati in esso specificato
     * @return int
     */
    public static function Count($filter = null) {
        return parent::_count(self::TABLE_NAME, $filter);
    }

    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0, $totCount = false) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset, $totCount);
    }

    public function Save() {
        global $con, $LoggedAccount;
        $this->ID_UID_FIREBASE_ADMIN = $LoggedAccount->ID;
        $vars = get_object_vars($this);
        unset($vars["DATA_INSERIMENTO"]);
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars);
        $queryabi = $con->prepare($sql);
        $ins = false;
        foreach ($vars as $k => $v) {
            if ($k == "ID" && $v == 0) {
                $ins = true;
                continue;
            }
            $queryabi->bindValue(":" . $k, $v);
//            if ($k == "DATA_INSERIMENTO" && $ins) {
//                $queryabi->bindValue(":" . $k, date('d-m-Y'));
//            } else {
//                $queryabi->bindValue(":" . $k, $v);
//            }
        }

        try {
            $it = parent::_Save($queryabi);
        } catch (Exception $exc) {
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }

    public function Delete() {
        return parent::_Delete(self::TABLE_NAME, "ID = " . $this->ID);
    }

    /**
     * Load List Faq with PDO connection
     */
    public function Load($id_categoria = 0, $cancellato = 0) {
        global $con;
        $where = "PUBBLICATA = 1 ";
        $order = " ORDER BY DATA_INSERIMENTO";
        if (intval($cancellato) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "CANCELLATO = :cancellato";
        if (intval($id_categoria) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "ID_CATEGORIA = :id_categoria";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";

        $query = $con->prepare($sql);
        if (intval($cancellato) >= 0)
            $query->bindParam(":CANCELLATO", intval($cancellato));
        if (intval($id_categoria) >= 0)
            $query->bindParam(":ID_CATEGORIA", intval($id_categoria));
        try {
            $query->execute();
            $res = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $res['esito'] = -999;
            $res['descrizioneErrore'] = $exc->getMessage();
        }
        return $res;
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function LogicalDelete() {
        return parent::_LogicalDelete(self::TABLE_NAME, "ID = " . $this->ID);
    }

    public function Pubblica($param = -1) {
        global $con;

        if ($param >= 0) {
            $sql = "UPDATE " . self::TABLE_NAME . " SET PUBBLICATA = $param WHERE ID = " . $this->ID;
            $query = $con->prepare($sql);
            if ($query->execute()) {
                try {
                    $return['esito'] = 1;
                } catch (Exception $exc) {
                    $return['esito'] = -999;
                    $return['descrizioneErrore'] = $exc->getMessage();
                }
            }
        } else {
            $return['esito'] = -999;
        }
        return $return;
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

}

class FaqUtenti extends DataClass {

    const TABLE_NAME = "FAQ_UTENTI";
    const SEQ_NAME = "FAQ_UTENTI_ID_SEQ";
    const SP_ = "SPFAQUTENTISAVE";

    public $ID = 0; // Primary key    
    public $ACCOUNT = '';
    public $DOMANDA = "";
    public $DATA_INSERIMENTO = null;
    public $NOME = '';
    public $COGNOME = '';
    public $EMAIL = '';
    public $STATO = 0;
    public $ID_CATEGORIA = 0;

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

            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE ID = :id";
            $query = $con->prepare($sql);
            $query->bindParam(":id", $src);
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
//                $it[0]['DOMANDA'] = html_entity_decode(htmlspecialchars_decode(stream_get_contents($it[0]['DOMANDA'])));
                Utils::FillObjectFromRow($this, $it[0]);
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }
    }

    public static function load($id = 0) {
        global $con;
        $row = array();
        if ($id > 0) {
            $sql = "SELECT F.ID, F.ID_CATEGORIA, F.DOMANDA, F.DATA_INSERIMENTO, F.STATO, A.NOME, A.COGNOME, N.CODICE_FISCALE "
                    . "FROM " . self::TABLE_NAME . " F INNER JOIN  " . Account::TABLE_NAME . " N ON N.CODICE_FISCALE = F.ACCOUNT "
                    . "LEFT JOIN  " . AccountAnagrafica::TABLE_NAME . " A ON A.CODICE_FISCALE = F.ACCOUNT WHERE F.ID = :id ";
            $query = $con->prepare($sql);
            $query->bindParam(":id", $id);
            try {
                $query->execute();
                $row = $query->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }
        return $row;
    }

    /**
     * Restituisce il totale i
     *
     * @param bool $filter Se specificato, filtra i comune in funzione dei parametri specificati in esso specificato
     * @return int
     */
    public static function Count($filter = null) {
        return parent::_count(self::TABLE_NAME, $filter);
    }

    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }

    public static function LoadDataTableFaqUtenti($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0, $totCount = true) { //, $cblobColumn= array()
        $select = " F.ID, F.ID_CATEGORIA, F.DOMANDA, F.DATA_INSERIMENTO, F.STATO, A.NOME, A.COGNOME, N.CODICE_FISCALE, A.SPID_CELLULARE ";
        $table = self::TABLE_NAME . " F INNER JOIN  " . Account::TABLE_NAME . " N ON N.CODICE_FISCALE = F.ACCOUNT  LEFT JOIN  " . AccountAnagrafica::TABLE_NAME . " A ON A.CODICE_FISCALE = F.ACCOUNT ";
        return parent::_loadDataTableEx($select, $table, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset, $totCount);
    }

    public function Save() {
        global $con;
        $vars = get_object_vars($this);
        unset($vars['DATA_INSERIMENTO']);
//        Utils::print_array($vars);
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars);
        $query = $con->prepare($sql);
        foreach ($vars as $k => $v) {
            if ($k == "ID" && $v == 0) {
                continue;
            }
            $query->bindValue(":" . $k, $v);
        }
        try {
            $it = parent::_Save($query);
        } catch (Exception $exc) {
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }

    public function Delete() {
        return parent::_Delete(self::TABLE_NAME, "ID = " . $this->id);
    }

    public function PresaVisione($param = -1) {
        global $con;

        if ($param >= 0) {
            $sql = "UPDATE " . self::TABLE_NAME . " SET STATO = $param WHERE ID = " . $this->ID;
            $query = $con->prepare($sql);

            try {
                $return = parent::_Save($query);
            } catch (Exception $exc) {
                $return['esito'] = -999;
                $return['descrizioneErrore'] = $exc->getMessage();
            }
        } else {
            $return['esito'] = -999;
        }
        return $return;
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function LogicalDelete() {
        return parent::_LogicalDelete(self::TABLE_NAME, "ID = " . $this->id);
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

}
