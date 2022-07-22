<?php

/**
 * Description of Biblioteca
 *
 * @author Anselmo
 */
require_once ROOT . "/lib/classes/IDataClass.php";

class Biblioteca extends DataClass{
    const TABLE_NAME = "BIBLIOTECA";
    public $ID = 0;
    public $TITOLO = '';
    public $SOTTOTITOLO = '';
    public $AUTORE = '';
    public $GENERE = '';
    public $CATEGORIA = 0;
    public $UBICAZIONE = '';
    public $CANCELLATO = 0;
    public $NOTE = '';
    public $COPERTINA = '';
    public $TIPO_CONTENUTO = 0;

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

    public static function autocomplete($string = '', $gruppo = '') {
        global $con;
        $return = array();
        if (strlen($string) >= 1) {
            $string = "%" . $string . "%";
            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE ( TITOLO LIKE :TERM1 OR SOTTOTITOLO LIKE :TERM1  ) AND CANCELLATO = 0 ";
            $sql .= " ORDER BY TITOLO ASC";
            $query = $con->prepare($sql);            
            $query->bindParam(":TERM1", $string);
            try {
                $query->execute();
                
                while ($it = $query->fetch(PDO::FETCH_ASSOC)) {
                    $row['id'] = $it['ID'];
                    $row['text'] = $it['TITOLO'];
                    $row['label'] = $it['TITOLO'];                    
                    array_push($return, $row);
                }
            } catch (Exception $exc) {
                echo $exc->getMessage();
            }
        }
        return $return;
    }

    public function Load($categoria = '', $cancellato = 0) {
        global $con;
        $where = "";
        $order = " ORDER BY TITOLO";
        
        if (intval($cancellato) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "CANCELLATO = :CANCELLATO";
        if (!empty($categoria))
            $where .= ($where == "" ? "" : " AND ") . "CATEGORIA = :CATEGORIA";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";

        //echo $id_ditta.$sql; exit();

        $query = $con->prepare($sql);
        if (intval($cancellato) >= 0)
            $query->bindParam(":CANCELLATO", intval($cancellato));
        if (!empty($categoria))
            $query->bindParam(":CATEGORIA", $categoria);
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

    public function Save() {
        global $con;        
        $vars = get_object_vars($this);        
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars);
        $queryabi = $con->prepare($sql);
        //Utils::print_array($queryabi);
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

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function Delete() {
        return parent::_Delete(self::TABLE_NAME, " ID = " . $this->ID);
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function LogicalDelete() {

        return parent::_LogicalDelete(self::TABLE_NAME, ' ID = ' . $this->ID);
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