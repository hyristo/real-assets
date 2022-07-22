<?php
require_once "IDataClass.php";
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Logs
 *
 * @author Anselmo
 */
class Logs extends DataClass {
    
    const TABLE_NAME = "LOGS";
    const SP_ = "SPLOGSSAVE";
	
    public $ID = 0;
    public $ID_OPERATORE = "";
    public $IP = "";
    public $USERNAME = "";
    public $DATA = null;
    public $MODULE = "";
    public $ACTION = "";
    public $OPERATION = "";
    public $INFO = "";
    public $OLDVALUE = "";
    
    
    /**
     * 
     * @global type $con
     * @param type $src
     * @return type
     */
    public function __construct ($src = null)
    {
        global $con;
        if ($src == null)
            return;
        if (is_array($src))
	{
            $this->_loadByRow($src, $stripSlashes);
            
        }elseif (is_numeric($src)) {
            // Carichiamo tramite ID
            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE ID = :ID";
            $query = $con->prepare($sql);
            $query->bindParam(":ID", $src);
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
                //$it[0]['INFO'] = stream_get_contents($it[0]['INFO']);                
                //$it[0]['OPERATION'] = stream_get_contents($it[0]['OPERATION']);                
                //$it[0]['OLDVALUE'] = stream_get_contents($it[0]['OLDVALUE']);                
                Utils::FillObjectFromRow($this, $it[0]);                
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }		
    }
    
    
    public function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }
    
    public static function Load ($Dal = "", $Al = "", $id_operatore = "", $action = "")
    {
            global $con;
            $where = "";
            $order = " ID desc, DATA desc";
            if ($Dal)
                $Dal = Utils::FormatDate($Dal, DATE_FORMAT_ISO);
            if ($Al)
                $Al = Utils::FormatDate($Al, DATE_FORMAT_ISO);

            if ($Dal && $Al)
                $where = "(DATA BETWEEN :data_dal AND :data_al )";
            elseif ($Dal)
                $where = "(DATA >= :data_dal)";
            elseif ($Al)
                $where = "(DATA <= :data_al)";

            if (intval($id_operatore) > 0)
                    $where .= ($where == "" ? "" : " AND ") . "ID_OPERATORE = :id_operatore ";

            if ($action != "")
                    $where .= ($where == "" ? "" : " AND ") . "ACTION LIKE ':action%'";

            $sql = "SELECT * FROM " . self::TABLE_NAME;
            if ($where != "")
                $sql .= " WHERE $where";
            if ($order != "")
                $sql .= " ORDER BY $order";
            //echo $sql; exit();

            $query = $con->prepare($sql);
            
            if ($Dal && $Al){
                $query->bindParam(":data_dal", $Dal);
                $query->bindParam(":data_al", $Al);
            }elseif ($Dal) {
                $query->bindParam(":data_dal", $Dal);
            }elseif ($Al) {
                $query->bindParam(":data_al", $Al);
            }
            
            if (intval($id_operatore) > 0)
                $query->bindParam(":id_operatore", intval($id_operatore));
            if ($action !="")
                $query->bindParam(":action", $action);
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                $exc->getMessage();
            }
            return $it;

            
    }
    
    /**
     * Funzione di salvataggio dei logs
     * @global type $con
     * @return type
     */
    public function Save()
    {
        global $con, $LoggedAccount;
        $vars = get_object_vars($this);
        
        $sql = "CALL " . self::SP_ . " (?,?,?,?,?,?,?,?)";
        //echo $sql;
        $query = $con->prepare($sql, false);
        
        $query->bindParam(1, $this->ID_OPERATORE);
        $query->bindParam(2, $this->IP, PDO::PARAM_STR);
        $query->bindParam(3, $LoggedAccount->ID, PDO::PARAM_STR);
        $query->bindParam(4, $this->MODULE, PDO::PARAM_STR);
        $query->bindParam(5, $this->ACTION, PDO::PARAM_STR);
        $query->bindParam(6, $this->OPERATION, PDO::PARAM_STR, strlen($this->OPERATION));
        $query->bindParam(7, $this->INFO, PDO::PARAM_STR, strlen($this->INFO));
        $query->bindParam(8, $this->OLDVALUE, PDO::PARAM_STR, strlen($this->OLDVALUE));
        //Utils::print_array($this);exit();
        try {
            if($query->execute()){
               $it['esito'] = 1;
            }
            
        } catch (Exception $exc) {
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;

    }
    
    /**
     * Restituisce la mappatura dei campi con i relativi valori modificati
     * @param type $record
     * @return type
     */
    public function ParseMessage(&$record)
    {
        $testo = $this->INFO;
        $classe = $this->MODULE;
        $oldValue = $this->OLDVALUE;

        if(strtolower($classe) == "login" || strtolower($classe) == "logout")
        {
            return array();
        }


        $a = json_decode($testo, true);
        //Utils::print_array($this);exit();
        $obj = new $classe(json_decode($testo, true));
        //echo "<pre>".print_r($testo, true)."</pre>";
        //echo "<pre>".print_r($obj, true)."</pre>";
        $record = array();

        $props = get_class_vars(get_class($obj));
        foreach($props as $k => $value)
        {
            
                
            $fieldLog = array();
            $fieldOldLog = array();
            $obj->Parse($fieldLog, $k);

            $oldObj = new $classe(json_decode($oldValue, TRUE));
            $oldObj->Parse($fieldOldLog, $k);

            if($fieldLog[0]['Campo'] != "")
            {
                if(Date::is_date($fieldLog[0]['Valore']))
                {
                        $fieldLog[0]['Valore'] = Date::FormatDate($fieldLog[0]['Valore'], DATE_FORMAT_ITA);

                        //$oldObj->$k = Utils::FormatDate($oldObj->$k, DATE_FORMAT_ITA);
                }
                if(Date::is_date($fieldOldLog[0]['Valore']))
                {
                        $fieldOldLog[0]['Valore'] = Date::FormatDate($fieldOldLog[0]['Valore'], DATE_FORMAT_ITA);

                        //$oldObj->$k = Utils::FormatDate($oldObj->$k, DATE_FORMAT_ITA);
                }
                
                if(Date::is_date($obj->$k))
                    $obj->$k = Date::FormatDate($fieldOldLog[0]['Valore'], DATE_FORMAT_ITA);
                if(Date::is_date($oldObj->$k))
                    $oldObj->$k = Date::FormatDate($fieldOldLog[0]['Valore'], DATE_FORMAT_ITA);
                
                if(trim($obj->$k) != trim($oldObj->$k))
                {    
                    //$record[] = array("Campo" => "<div style=\"font-weight:bold; color: red;\">".$fieldLog[0]['Campo']."</div>", "Valore" => "<div style=\"font-weight:bold; color: red;\">".$fieldLog[0]['Valore']."</div>", "OldValue"=>"<div style=\"font-weight:bold; color: red;\">".$fieldOldLog[0]['Valore']."</div>");
                    $record[] = array("Campo" => $fieldLog[0]['Campo'], "Valore" => $fieldLog[0]['Valore'], "OldValue" => $fieldOldLog[0]['Valore'], "Variato" => true);
                }
                else $record[] = array("Campo" => $fieldLog[0]['Campo'], "Valore" => $fieldLog[0]['Valore'], "OldValue" => $fieldOldLog[0]['Valore'], "Variato" => false);

            }
        }

        return $record;
    }
    
    /**
     * Verifica se un campo è stato modificato
     * @param type $field
     * @param type $valore
     * @param type $classe
     * @return boolean
     */
    public function fieldModified($field, $valore, $classe)
    {
            $record = $this->ParseMessage();

            if(Utils::is_date($valore))
                    $valore = Utils::FormatDate($valore, DATE_FORMAT_ITA);

            foreach($record as $k => $value)
            {
                    if(trim($value['Campo']) == $field)
                    {
                            if(trim($value['Valore']) != trim($valore))
                                    return true;
                    }
            }
            return false;
    }
    
}


/**
 * Description of Logs
 *
 * @author Anselmo
 */
class LogsBe extends DataClass {
    
    const TABLE_NAME = "LOGS_BE";
    const SP_ = "SPLOGSBESAVE";
	
    public $ID = 0;
    public $ID_OPERATORE = "";
    public $IP = "";
    public $USERNAME = "";
    public $DATA = null;
    public $MODULE = "";
    public $ACTION = "";
    public $OPERATION = "";
    public $INFO = "";
    public $OLDVALUE = "";
    
    
    /**
     * 
     * @global type $con
     * @param type $src
     * @return type
     */
    public function __construct ($src = null)
    {
        global $con;
        if ($src == null)
            return;
        if (is_array($src))
	{
            $this->_loadByRow($src, $stripSlashes);
            
        }elseif (is_numeric($src)) {
            // Carichiamo tramite ID
            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE ID = :id";
            $query = $con->prepare($sql);
            $query->bindParam(":id", $src);
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
                $it[0]['INFO'] = stream_get_contents($it[0]['INFO']);                
                $it[0]['OPERATION'] = stream_get_contents($it[0]['OPERATION']);                
                $it[0]['OLDVALUE'] = stream_get_contents($it[0]['OLDVALUE']);                
                Utils::FillObjectFromRow($this, $it[0]);                
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }		
    }
    
    
    public function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }
    
    public static function Load ($Dal = "", $Al = "", $id_operatore = "", $action = "")
    {
            global $con;
            $where = "";
            $order = " ID desc, DATA desc";
            if ($Dal)
                $Dal = Date::FormatDate($Dal, DATE_FORMAT_ISO);
            if ($Al)
                $Al = Date::FormatDate($Al, DATE_FORMAT_ISO);

            if ($Dal && $Al)
                $where = "(DATA BETWEEN :data_dal AND :data_al )";
            elseif ($Dal)
                $where = "(DATA >= :data_dal)";
            elseif ($Al)
                $where = "(DATA <= :data_al)";

            if (intval($id_operatore) > 0)
                    $where .= ($where == "" ? "" : " AND ") . "ID_OPERATORE = :id_operatore ";

            if ($action != "")
                    $where .= ($where == "" ? "" : " AND ") . "ACTION LIKE ':action%'";

            $sql = "SELECT * FROM " . self::TABLE_NAME;
            if ($where != "")
                $sql .= " WHERE $where";
            if ($order != "")
                $sql .= " ORDER BY $order";
            //echo $sql; exit();

            $query = $con->prepare($sql);
            
            if ($Dal && $Al){
                $query->bindParam(":data_dal", $Dal);
                $query->bindParam(":data_al", $Al);
            }elseif ($Dal) {
                $query->bindParam(":data_dal", $Dal);
            }elseif ($Al) {
                $query->bindParam(":data_al", $Al);
            }
            
            if (intval($id_operatore) > 0)
                $query->bindParam(":id_operatore", intval($id_operatore));
            if ($action !="")
                $query->bindParam(":action", $action);
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                $exc->getMessage();
            }
            return $it;

            
    }
    
    /**
     * Funzione di salvataggio dei logs
     * @global type $con
     * @return type
     */
    public function Save()
    {
        global $con, $LoggedAccount;
        $vars = get_object_vars($this);
        
        $sql = "CALL " . self::SP_ . " (?,?,?,?,?,?,?,?)";
                
        $query = $con->prepare($sql);

        $query->bindParam(1, $this->ID_OPERATORE);
        $query->bindParam(2, $this->IP);
        $query->bindParam(3, $this->USERNAME);
        $query->bindParam(4, $this->MODULE);
        $query->bindParam(5, $this->ACTION);
        $query->bindParam(6, $this->OPERATION);
        $query->bindParam(7, $this->INFO);
        $query->bindParam(8, $this->OLDVALUE);
        
        try {
            if($query->execute()){
               $it['esito'] = 1;
            }
            
        } catch (Exception $exc) {
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;

    }
    
    /**
     * Restituisce la mappatura dei campi con i relativi valori modificati
     * @param type $record
     * @return type
     */
    public function ParseMessage(&$record)
    {
        $testo = $this->INFO;
        $classe = $this->MODULE;
        $oldValue = $this->OLDVALUE;

        if($classe == "login" || $classe == "logout")
        {
            return array();
        }
        $a = json_decode($testo, true);
        //Utils::print_array($this);exit();
        $obj = new $classe(json_decode($testo, true));

        //echo "<pre>".print_r($obj, true)."</pre>";
        $record = array();

        $props = get_class_vars(get_class($obj));
        foreach($props as $k => $value)
        {
            
                
            $fieldLog = array();
            $fieldOldLog = array();
            $obj->Parse($fieldLog, $k);

            $oldObj = new $classe(json_decode($oldValue, TRUE));
            $oldObj->Parse($fieldOldLog, $k);

            if($fieldLog[0]['Campo'] != "")
            {
                if(Date::is_date($fieldLog[0]['Valore']))
                {
                        $fieldLog[0]['Valore'] = Date::FormatDate($fieldLog[0]['Valore'], DATE_FORMAT_ITA);

                        //$oldObj->$k = Date::FormatDate($oldObj->$k, DATE_FORMAT_ITA);
                }
                if(Date::is_date($fieldOldLog[0]['Valore']))
                {
                        $fieldOldLog[0]['Valore'] = Date::FormatDate($fieldOldLog[0]['Valore'], DATE_FORMAT_ITA);

                        //$oldObj->$k = Date::FormatDate($oldObj->$k, DATE_FORMAT_ITA);
                }
                
                if(Date::is_date($obj->$k))
                    $obj->$k = Date::FormatDate($fieldOldLog[0]['Valore'], DATE_FORMAT_ITA);
                if(Date::is_date($oldObj->$k))
                    $oldObj->$k = Date::FormatDate($fieldOldLog[0]['Valore'], DATE_FORMAT_ITA);
                
                if(trim($obj->$k) != trim($oldObj->$k))
                {    
                    //$record[] = array("Campo" => "<div style=\"font-weight:bold; color: red;\">".$fieldLog[0]['Campo']."</div>", "Valore" => "<div style=\"font-weight:bold; color: red;\">".$fieldLog[0]['Valore']."</div>", "OldValue"=>"<div style=\"font-weight:bold; color: red;\">".$fieldOldLog[0]['Valore']."</div>");
                    $record[] = array("Campo" => $fieldLog[0]['Campo'], "Valore" => $fieldLog[0]['Valore'], "OldValue" => $fieldOldLog[0]['Valore'], "Variato" => true);
                }
                else $record[] = array("Campo" => $fieldLog[0]['Campo'], "Valore" => $fieldLog[0]['Valore'], "OldValue" => $fieldOldLog[0]['Valore'], "Variato" => false);

            }
        }

        return $record;
    }
    
    /**
     * Verifica se un campo è stato modificato
     * @param type $field
     * @param type $valore
     * @param type $classe
     * @return boolean
     */
    public function fieldModified($field, $valore, $classe)
    {
            $record = $this->ParseMessage();

            if(Date::is_date($valore))
                    $valore = Date::FormatDate($valore, DATE_FORMAT_ITA);

            foreach($record as $k => $value)
            {
                    if(trim($value['Campo']) == $field)
                    {
                            if(trim($value['Valore']) != trim($valore))
                                    return true;
                    }
            }
            return false;
    }
    
}