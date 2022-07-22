<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CodiciVari
 *
 * @author Anselmo
 */
require_once ROOT . "lib/classes/IDataClass.php";

class CodiciVari extends DataClass {

    const TABLE_NAME = "CODICI_VARI";

    public $ID_CODICE = 0;
    public $GRUPPO = "";
    public $DESCRIZIONE = "";
    public $SIGLA = "";
    public $CANCELLATO = 0;
    public $INVISIBILE = 0;
    public $MODIFICABILE = 0;

    //put your code here

    public function __construct($src = null) {
        global $con;
        if ($src == null)
            return;
        if (is_array($src)) {
            $this->_loadByRow($src, $stripSlashes);
        } elseif (intval($src)) {
            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE ID_CODICE = :id_codice";
            $query = $con->prepare($sql);
            $query->bindParam(":id_codice", $src);
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
                Utils::FillObjectFromRow($this, $it[0]);
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }
    }

    public static function autocomplete($string = '', $gruppo = '') {
        global $con;
        $return = array();
        if (strlen($string) >= 1) {
            $string = "%" . $string . "%";
            $sql = "SELECT ID_CODICE as ID, DESCRIZIONE as label, SIGLA FROM " . self::TABLE_NAME . " WHERE ( DESCRIZIONE LIKE :term1 OR SIGLA LIKE :term2 ) AND GRUPPO = :gruppo AND CANCELLATO = 0 ";
            $sql .= " ORDER BY DESCRIZIONE ASC";
            $query = $con->prepare($sql);
            $query->bindParam(":GRUPPO", $gruppo);
            $query->bindParam(":TERM1", $string);
            $query->bindParam(":TERM2", $string);
            try {
                $query->execute();
                $return = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $return;
    }

    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }

    public static function Load($id = 0, $gruppo = "", $invisibile = 0, $cancellato = 0) {
        global $con;
        $where = "";
        $order = ' ORDER BY DESCRIZIONE';
        if (intval($id) > 0)
            $where .= ($where == "" ? "" : " AND ") . "ID_CODICE = :id_codice";
        if (trim($gruppo) != "")
            $where .= ($where == "" ? "" : " AND ") . "GRUPPO = :gruppo";
        if (intval($invisibile) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "INVISIBILE = :invisibile";
        if (intval($cancellato) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "CANCELLATO = :cancellato";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";
        
        $query = $con->prepare($sql, true);        
        if (intval($id) > 0)
            $query->bindParam(":id_codice", intval($id));
        if (trim($gruppo) != "")
            $query->bindParam(":gruppo", trim($gruppo));
        if (intval($invisibile) >= 0)
            $query->bindParam(":invisibile", intval($invisibile));
        if (intval($cancellato) >= 0)
            $query->bindParam(":cancellato", intval($cancellato));

        try {
            $query->execute();
            if (intval($id) > 0) {
                $it = $query->fetch(PDO::FETCH_ASSOC);
            } else {

                $it = $query->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }        
        return $it;
    }

    public static function getNextID($gruppo) {
        global $con;
        $sql = ' SELECT MAX(ID_CODICE) as IX from ' . self::TABLE_NAME . ' where GRUPPO = :GRUPPO group by GRUPPO';
        
        $query = $con->prepare($sql);
        $query->bindParam(":GRUPPO", trim($gruppo));
        
        //Utils::print_array($query);exit();
        
        try {
            $query->execute();
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        
        return $it[0]['IX'];
    }

    public static function Count($filter = null) {
        return parent::_count(self::TABLE_NAME, $filter);
    }

    public function Save() {
        global $con;
        $it = Utils::initDefaultResponse();
        $this->ID_CODICE = $this->ID_CODICE <= 0 ? (self::getNextID(trim($this->GRUPPO)) + 1) : $this->ID_CODICE;
        
        $control = $this->Count(" GRUPPO = '" . trim($this->GRUPPO) . "' AND ID_CODICE = " . $this->ID_CODICE);

        if ($control > 0) {
            $sql = "UPDATE " . self::TABLE_NAME . "  SET DESCRIZIONE = :DESCRIZIONE, SIGLA = :SIGLA, INVISIBILE = :INVISIBILE, CANCELLATO = :CANCELLATO, MODIFICABILE = :MODIFICABILE  WHERE GRUPPO = :GRUPPO and ID_CODICE = :ID_CODICE ";
        } else {
            $sql = "INSERT INTO " . self::TABLE_NAME . " ( ID_CODICE , GRUPPO , DESCRIZIONE , SIGLA , CANCELLATO , INVISIBILE , MODIFICABILE ) VALUES ( :ID_CODICE , :GRUPPO, :DESCRIZIONE , :SIGLA, :CANCELLATO , :INVISIBILE ,  :MODIFICABILE)";
        }
        $vars = get_object_vars($this);
        $queryabi = $con->prepare($sql);
        foreach ($vars as $k => $v) {
            $queryabi->bindValue(":" . $k, $v);
        }
        try {
            $it = parent::_Save($queryabi);
        } catch (Exception $exc) {
            $it['esito'] = -999;
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
        return parent::_Delete(self::TABLE_NAME, "ID_CODICE = " . $this->ID_CODICE . " and GRUPPO = '" . trim($this->GRUPPO) . "' ");
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function LogicalDelete() {
        return parent::_LogicalDelete(self::TABLE_NAME, "ID_CODICE = " . $this->ID_CODICE . " and GRUPPO = '" . trim($this->GRUPPO) . "' ");
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
