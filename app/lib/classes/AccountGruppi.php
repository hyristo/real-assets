<?php


require_once "IDataClass.php";
/**
 * Description of AccountGruppi
 *
 * @author Anselmo
 */
class AccountGruppi extends DataClass {
    
    const TABLE_NAME = 'ACCOUNTGRUPPI';

    public $ID = 0;
    public $NOME = "";
    public $CANCELLATO = 0;
    
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
    
    
    public static function Load($cancellato = -1) {
        global $con;
        $where = "";
        $order = ' ORDER BY "NOME"';
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
    
    public static function Count($filter = null) {
        return parent::_count(self::TABLE_NAME, $filter);
    }
    
    public function Save() {
        global $con, $LoggedAccount;
        $vars = get_object_vars($this);
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars);
        $queryabi = $con->prepare($sql);
        foreach ($vars as $k => $v) {
            if ($k == "ID" && $v == 0)
                continue;
            $queryabi->bindValue(":" . $k, $v);
            //echo $k." => ". $v."<br>";
        }
        //echo $sql;
        try {
            $it = parent::_Save($queryabi);
        } catch (Exception $exc) {
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }
    
    /**
     * 
     * @param type $searchQuery
     * @param type $searchArray
     * @param type $columnName
     * @param type $columnSortOrder
     * @param type $start
     * @param type $offset
     * @return type
     */
    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }
    
    /**
     * 
     * @return type
     */
    public function Delete() {
        return parent::_LogicalDelete(self::TABLE_NAME, "id = " . $this->id);
    }
    
    
    public static function autocomplete($string = '') {
        global $con;
        $return = array();
        if (strlen($string) >= 3) {
            $string = "%" . strtoupper($string) . "%";
            //$sql = "SELECT id, descrizione as label FROM " . self::TABLE_NAME . " WHERE descrizione LIKE '%".strtoupper($string)."%' AND valido = 1 ORDER BY descrizione ASC";            
            $sql = "SELECT NOME as LABEL FROM " . self::TABLE_NAME . " WHERE NOME LIKE :term AND CANCELLATO = 0 ORDER BY NOME ASC";
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

    public function GetPermessi() {
        return AccountPermessi::Load($this->ID);
    }
    
    /**
     * 
     * @param type $record
     * @param type $campo
     */
    public function Parse(&$record, $campo = "") {
        $props = get_class_vars(get_class($this));
        array_push($record, array("Campo" => $campo, "Valore" => $this->$campo));
    }
    
}

class AccountPermessi extends DataClass {

    const TABLE_NAME = 'ACCOUNTPERMESSI';

    public $ID_GRUPPO = 0;
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
    
    public function Load($id_gruppo = 0, $modulo = "", $sotto_modulo = "", $read = "", $update = "", $delete = "", $disabled = "") {
        global $con;
        $where = "";
        $order = ' ORDER BY SOTTO_MODULO ';
        if ($id_gruppo > 0)
            $where .= ($where == "" ? "" : " AND ") . "ID_GRUPPO = :ID_GRUPPO";
        if ($modulo != "")
            $where .= ($where == "" ? "" : " AND ") . "MODULO = :MODULO";
        if ($sotto_modulo != "")
            $where .= ($where == "" ? "" : " AND ") . "SOTTO_MODULO = :SOTTO_MODULO";
        if ($read != "")
            $where .= ($where == "" ? "" : " AND ") . "READ = :READ";
        if ($update != "")
            $where .= ($where == "" ? "" : " AND ") . "UPDATE = :UPDATE";
        if ($delete != "")
            $where .= ($where == "" ? "" : " AND ") . "DELETE = :DELETE";
        if ($disabled != "")
            $where .= ($where == "" ? "" : " AND ") . "DISABLED = :DISABLED";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";

        //echo $sql; exit();

        $query = $con->prepare($sql, true);
        if ($id_gruppo > 0)
            $query->bindParam(":ID_GRUPPO", $id_gruppo);
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

        if ($this->ID_GRUPPO <= 0 || $this->MODULO == "" || $this->SOTTO_MODULO == "")
            return array();

        $control = $this->Count('ID_GRUPPO = ' . $this->ID_GRUPPO . ' AND MODULO = \'' . $this->MODULO . '\' AND sotto_modulo = \'' . $this->SOTTO_MODULO . '\'');
        if ($control > 0) {
            $sql = "UPDATE  " . self::TABLE_NAME . "  SET read = :read , update = :update , delete = :delete , disabled = :disabled WHERE id_gruppo = :id_gruppo AND modulo = :modulo AND sotto_modulo = :sotto_modulo  ";
        } else {
            $sql = "INSERT INTO " . self::TABLE_NAME . " ( id_gruppo , modulo , sotto_modulo , read , update , delete , disabled ) VALUES ( :id_gruppo , :modulo , :sotto_modulo, :read , :update, :delete , :disabled )";
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
        return parent::_LogicalDelete(self::TABLE_NAME, "id = " . $this->id);
    }
    
    public function Parse(&$record, $campo = "") {
        $props = get_class_vars(get_class($this));
        array_push($record, array("Campo" => $campo, "Valore" => $this->$campo));
    }
}