<?php
require_once ROOT . "/lib/classes/IDataClass.php";

class Locatari extends DataClass
{
    const TABLE_NAME = "LOCATARI";
    public $ID = 0;
    public $NOME = '';
    public $COGNOME = '';
    public $DATA_NASCITA = NULL;
    public $DATA_MORTE = NULL;
    public $SESSO = '';
    public $CODICE_FISCALE = '';
    public $INDIRIZZO = '';
    public $CIVICO = '';
    public $COMUNE = '';
    public $PROVINCIA = '';
    public $CANCELLATO = 0;
    public $recapiti = array();

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

    public function loadDati($src = array()) {
        $this->recapiti = $this->loadRecapiti();
    }
    public function loadRecapiti(){
        return RecapitiLocatari::Load($this->ID);
    }


    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }

    public static function autocomplete($string = '') {
        global $con;
        $return = array();
        if (strlen($string) >= 3) {
            $filter = '';

            $string = "%" . strtolower($string) . "%";
            $sql = "SELECT ID, CODICE_FISCALE  , COGNOME , NOME FROM " . self::TABLE_NAME . " WHERE  " . $filter . " (LOWER(CODICE_FISCALE) LIKE :TERM OR LOWER(COGNOME) LIKE :TERM OR LOWER(NOME) LIKE :TERM )  ORDER BY COGNOME, NOME";
            $query = $con->prepare($sql,true);
            $query->bindParam(":TERM", $string);
            try {
                $query->execute();
                while ($it = $query->fetch(PDO::FETCH_ASSOC)) {
                    $descrizione = $it['COGNOME']. ' ' . $it['NOME'] . ' (' .$it['CODICE_FISCALE'] .')';
                    $row['id'] = $it['ID'];
                    $row['text'] = $descrizione;
                    $row['label'] = $descrizione;
                    $row['codice_fiscale'] = $it['CODICE_FISCALE'];
                    array_push($return, $row);
                }
//                $return = $query->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $exc) {
                echo $exc->getMessage();
//                $return = "ERROR";
            }
        }
        return $return;
    }

    public function Save() {
        global $con;
        $vars = get_object_vars($this);
        unset($vars['recapiti']);
        $sql = Utils::prepareQuery(self::TABLE_NAME, $vars);
        $queryabi = $con->prepare($sql);
        foreach ($vars as $k => $v) {
            if ($k == "ID" && ($v == 0 || $v == ''))
                continue;
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