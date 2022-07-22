<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once "IDataClass.php";
/**
 * Description of Moduli
 *
 * @author Anselmo
 */
class Moduli extends DataClass {
    //put your code here
    
    const TABLE_NAME = 'APP_MODULI';

    public $CODICE = "";
    public $DESCRIZIONE = "";
    public $CANCELLATO = 0;
    
    public function __construct($src = null) {
        global $con;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['CODICE']) && $src['CODICE'] != '') {
                $this->_loadAndFillObject($src['CODICE'], self::TABLE_NAME, $src, 'CODICE');
            } else {
                $this->_loadByRow($src, $stripSlashes);
            }
        }
    }
    
    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }
    
    public static function Count($filter = null) {
        //echo $filter."####<br>";
        return parent::_count(self::TABLE_NAME, $filter);
    }
    
    public static function Load($cancellato = -1) {
        global $con;
        $where = "";
        $order = ' ORDER BY "CODICE"';
        if (intval($cancellato) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "CANCELLATO = :cancellato";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";
//        echo $sql;
        $query = $con->prepare($sql, true);
        if (intval($cancellato) >= 0)
            $query->bindParam(":CANCELLATO", intval($cancellato));

        try {
            $query->execute();            
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }
    
    public function Save() {
        global $con;

        $control = $this->Count("CODICE = '" . trim($this->CODICE) . "'");

        if ($control > 0) {
            $sql = "UPDATE  " . self::TABLE_NAME . "  SET descrizione = :descrizione, cancellato = :cancellato WHERE codice = :codice ";
        } else {
            $sql = "INSERT INTO " . self::TABLE_NAME . " ( codice , descrizione , cancellato ) VALUES ( :codice , :descrizione , :cancellato)";
        }
        
        $vars = get_object_vars($this);
        $queryabi = $con->prepare($sql, true);
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
    
    public function Delete() {
        return parent::_Delete(self::TABLE_NAME, "CODICE = " . $this->ID);
    }
    
    public static function autocomplete($string = '') {
        global $con;
        $return = array();
        if (strlen($string) >= 3) {
            $string = "%" . strtoupper($string) . "%";
            //$sql = "SELECT id, descrizione as label FROM " . self::TABLE_NAME . " WHERE descrizione LIKE '%".strtoupper($string)."%' AND valido = 1 ORDER BY descrizione ASC";            
            $sql = "SELECT CODICE as LABEL FROM " . self::TABLE_NAME . " WHERE CODICE LIKE :term AND CANCELLATO = 0 ORDER BY CODICE ASC";
            //echo $sql;
            $query = $con->prepare($sql, true);
            $query->bindParam(":TERM", $string);
            try {
                $query->execute();
                $return = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                echo $exc->getMessage();
//                $return = "ERROR";
            }
        }
        return $return;
    }
    
    public function Parse(&$record, $campo = "") {
        $props = get_class_vars(get_class($this));
        array_push($record, array("Campo" => $campo, "Valore" => $this->$campo));
    }
    
    
}


class SottoModuli extends DataClass {

    const TABLE_NAME = 'APP_SOTTO_MODULI';

    public $CODICE = "";
    public $NOME = "";
    public $DESCRIZIONE = "";
    public $CANCELLATO = 0;
    
    public function __construct($src = null) {
        global $con;
        if ($src == null)
            return;
        if (is_array($src)) {
            if (isset($src['CODICE']) && $src['CODICE'] != '') {
                $this->_loadAndFillObject($src['CODICE'], self::TABLE_NAME, $src, 'CODICE');
            } else {
                $this->_loadByRow($src, $stripSlashes);
            }
        }
    }
    
    
    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }
    
    public static function Count($filter = null) {
        return parent::_count(self::TABLE_NAME, $filter);
    }
    
    public static function Load($cancellato = -1) {
        global $con;
        $where = "";
        $order = ' ORDER BY "CODICE"';
        if (intval($cancellato) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "CANCELLATO = :cancellato";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";
//        echo $sql;
        $query = $con->prepare($sql, true);
        if (intval($cancellato) >= 0)
            $query->bindParam(":CANCELLATO", intval($cancellato));

        try {
            $query->execute();            
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }
    
    
    public function Save() {
        global $con;

        $control = $this->Count("CODICE = '" . trim($this->CODICE) . "'");

        if ($control > 0) {
            $sql = "UPDATE  " . self::TABLE_NAME . "  SET nome = :nome, descrizione = :descrizione, cancellato = :cancellato WHERE codice = :codice ";
        } else {
            $sql = "INSERT INTO " . self::TABLE_NAME . " ( codice , nome , descrizione , cancellato ) VALUES ( :codice , :nome , :descrizione , :cancellato )";
        }
        
        $vars = get_object_vars($this);
        $queryabi = $con->prepare($sql, true);
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
    
    public static function autocomplete($string = '') {
        global $con;
        $return = array();
        if (strlen($string) >= 3) {
            $string = "%" . strtoupper($string) . "%";
            //$sql = "SELECT id, descrizione as label FROM " . self::TABLE_NAME . " WHERE descrizione LIKE '%".strtoupper($string)."%' AND valido = 1 ORDER BY descrizione ASC";            
            $sql = "SELECT CODICE as LABEL FROM " . self::TABLE_NAME . " WHERE CODICE LIKE :term AND CANCELLATO = 0 ORDER BY CODICE ASC";
            //echo $sql;
            $query = $con->prepare($sql, true);
            $query->bindParam(":TERM", $string);
            try {
                $query->execute();
                $return = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                echo $exc->getMessage();
//                $return = "ERROR";
            }
        }
        return $return;
    }
    
    public function Parse(&$record, $campo = "") {
        $props = get_class_vars(get_class($this));
        array_push($record, array("Campo" => $campo, "Valore" => $this->$campo));
    }
    
}

class SottoModuliRelation extends DataClass {

    const TABLE_NAME = 'APP_MODULI_SOTTO_MODULI';

    public $MODULO = "";
    public $SOTTO_MODULO = "";
    public $READ = 0;
    public $UPDATE = 0;
    public $DELETE = 0;
    public $DISABLED = 0;

    //put your code here

    public function __construct($src = null) {
        global $con;
        if ($src == null)
            return;
        if (is_array($src)) {
            $this->_loadByRow($src, $stripSlashes);
        }
    }
    
    
    
    public function Load($modulo = "", $sotto_modulo = "", $read = "", $update = "", $delete = "", $disabled = "") {
        global $con;
        $where = "";
        $order = " ORDER BY MODULO ASC, SOTTO_MODULO ASC";
        if ($modulo != "")
            $where .= ($where == "" ? "" : " AND ") . "modulo = :modulo";
        if ($sotto_modulo != "")
            $where .= ($where == "" ? "" : " AND ") . "sotto_modulo = :sotto_modulo";
        if ($read != "")
            $where .= ($where == "" ? "" : " AND ") . "read = :read";
        if ($update != "")
            $where .= ($where == "" ? "" : " AND ") . "update = :update";
        if ($delete != "")
            $where .= ($where == "" ? "" : " AND ") . "delete = :delete";
        if ($disabled != "")
            $where .= ($where == "" ? "" : " AND ") . "disabled = :disabled";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where ";
        $sql .=" $order";
        //echo $sql; exit();

        $query = $con->prepare($sql, true);
        if ($modulo != "")
            $query->bindParam(":MODULO", $modulo);
        if ($sotto_modulo != "")
            $query->bindParam(":SOTTO_MODULO", $sotto_modulo);
        if ($read != "")
            $query->bindParam(":READ", $read);
        if ($update != "")
            $query->bindParam(":UPDATE", $update);
        if ($delete != "")
            $query->bindParam(":DELETE", $delete);
        if ($disabled != "")
            $query->bindParam(":DISABLED", $disabled);
        try {
            $query->execute();
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }
    
    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }
    
    public static function Count($filter = null) {
        return parent::_count(self::TABLE_NAME, $filter);
    }
    
    public function Save() {
        global $con;

        if ($this->MODULO == "" || $this->SOTTO_MODULO == "")
            return array();

        $control = $this->Count('MODULO = \'' . $this->MODULO . '\' AND SOTTO_MODULO = \'' . $this->SOTTO_MODULO . '\'');



        if ($control > 0) {
            $sql = "UPDATE  " . self::TABLE_NAME . "  SET read = :read, update = :update, delete = :delete, disabled = :disabled WHERE modulo = :modulo AND sotto_modulo = :sotto_modulo  ";
        } else {
            $sql = "INSERT INTO " . self::TABLE_NAME . " ( modulo , sotto_modulo , read , update , delete , disabled ) VALUES ( :modulo , :sotto_modulo , :read , :update , :delete , :disabled )";
        }
        
        $vars = get_object_vars($this);
        $queryabi = $con->prepare($sql, true);
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
    
    public function Delete() {
        return parent::_Delete(self::TABLE_NAME, "MODULO = " . $this->MODULO . " AND  SOTTO_MODULO = " . $this->SOTTO_MODULO);
    }
    
    public function Parse(&$record, $campo = "") {
        $props = get_class_vars(get_class($this));
        array_push($record, array("Campo" => $campo, "Valore" => $this->$campo));
    }
}
