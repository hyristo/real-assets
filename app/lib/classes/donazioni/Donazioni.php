<?php

/**
 * Description of Donazioni
 *
 * @author Anselmo
 */
require_once ROOT . "/lib/classes/IDataClass.php";

class Donazioni extends DataClass{
    const TABLE_NAME = "DONAZIONI";
    public $ID = 0;
    public $ID_CARD = 0;
    public $ANNO = 0;
    public $MESE = 0;
    public $IMPORTO = 0;
    public $TIPO_DONAZIONE = 0;
    public $PRO_DONAZIONE = 0;
    public $NOTE = '';    
    public $CANCELLATO = 0;
    public $DATA_MODIFICA = null;
    

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

    public function MaxData(){
        global $con;
        $sql = "SELECT max(DATA_MODIFICA) as AGGIORNAMENTO FROM " . self::TABLE_NAME;
        $query = $con->prepare($sql);
        try {
            $query->execute();
            $it = $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }

    public function Count() {
        
        $where = 'ID_CARD = '. $this->ID_CARD .' AND ANNO = '.$this->ANNO.' AND MESE = '.$this->MESE . ' AND TIPO_DONAZIONE = '.$this->TIPO_DONAZIONE.' AND PRO_DONAZIONE = '.$this->PRO_DONAZIONE;

        $ret = parent::_count(self::TABLE_NAME, $where);
        
        return $ret;
    }

    public function Load($codice = '',$anno = '', $mese = '', $tipo_donazione = 0, $pro_donazione = 0, $cancellato = 0) {
        global $con;
        $where = "";
        $order = " ORDER BY ANNO, MESE, PRO_DONAZIONE";
        
        if (intval($cancellato) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "CANCELLATO = :CANCELLATO";
        if (!empty($codice))
            $where .= ($where == "" ? "" : " AND ") . "ID_CARD = :ID_CARD";
        if (!empty($anno))
            $where .= ($where == "" ? "" : " AND ") . "ANNO = :ANNO";
        if (!empty($mese))
            $where .= ($where == "" ? "" : " AND ") . "MESE = :MESE";
        if (intval($tipo_donazione) > 0)
            $where .= ($where == "" ? "" : " AND ") . "TIPO_DONAZIONE = :TIPO_DONAZIONE";
        if (intval($pro_donazione) > 0)
            $where .= ($where == "" ? "" : " AND ") . "PRO_DONAZIONE = :PRO_DONAZIONE";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";

        //echo $id_ditta.$sql; exit();

        $query = $con->prepare($sql);
        if (intval($cancellato) >= 0)
            $query->bindParam(":CANCELLATO", intval($cancellato));
        if (!empty($codice))
            $query->bindParam(":ID_CARD", $codice);
        if (!empty($anno))
            $query->bindParam(":ANNO", $anno);
        if (!empty($mese))
            $query->bindParam(":MESE", $mese);
        if (intval($tipo_donazione) > 0)
            $query->bindParam(":TIPO_DONAZIONE", intval($tipo_donazione));
        if (intval($pro_donazione) > 0)
            $query->bindParam(":PRO_DONAZIONE", intval($pro_donazione));
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
        $vars = get_object_vars($this);
        $sendNotify = false;
        $vars['DATA_MODIFICA'] = date('Y-m-d');
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars);
        $queryabi = $con->prepare($sql);
        foreach ($vars as $k => $v) {
            if ($k == "ID" && ($v == 0 || $v == ''))
                continue;
//            echo ":" . $k.",".$v."<br>";
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

    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function Delete() {
        return parent::_Delete(self::TABLE_NAME, "ID = " . $this->ID);
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function LogicalDelete() {

        return parent::_LogicalDelete(self::TABLE_NAME, '"ID" = ' . $this->ID);
    }
    /**
     * Restituisce le statistiche totalei
     */
    public function GetStaticsPro($anno=""){
        global $con;
        $sql="SELECT C.DESCRIZIONE, SUM(D.IMPORTO) AS TOTALE FROM ".self::TABLE_NAME." D
        left join ".CodiciVari::TABLE_NAME." C on C.ID_CODICE = D.PRO_DONAZIONE AND GRUPPO = 'PRO_DONAZIONE'
        where D.CANCELLATO = 0 ";
        if(!empty($anno)){
            $sql.=" AND D.ANNO = :ANNO";    
        }
        $sql.=" group by C.DESCRIZIONE";
        $query = $con->prepare($sql);
        if(!empty($anno)){
            $query->bindValue(":ANNO", $anno);
        }
        try {
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $result['esito'] = -999;
            $result['descrizioneErrore'] = $exc->getMessage();
        }
        return $result;
    }

    public function GetStaticsProMese($anno=""){
        global $con;
        $sql="SELECT D.ANNO, D.MESE, C.DESCRIZIONE, SUM(D.IMPORTO) AS TOTALE FROM ".self::TABLE_NAME." D
        left join ".CodiciVari::TABLE_NAME." C on C.ID_CODICE = D.PRO_DONAZIONE AND GRUPPO = 'PRO_DONAZIONE'
        where D.CANCELLATO = 0 ";
        if(!empty($anno)){
            $sql.=" AND D.ANNO = :ANNO";    
        }
        $sql.=" group by C.DESCRIZIONE, D.ANNO, D.MESE";
        $query = $con->prepare($sql);
        if(!empty($anno)){
            $query->bindValue(":ANNO", $anno);
        }
        try {
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $result['esito'] = -999;
            $result['descrizioneErrore'] = $exc->getMessage();
        }
        return $result;
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