<?php

require_once "IDataClass.php";
/*
 * Gestione della parte di export composta da due classi
 * RichiesteExport: dati relativi ad una richiesta di export
 * RichiesteExportMerce: dati relativi alle merci di una richiesta
 */

/**
 * Description of Attivita
 *
 * @author Stefano
 */
class Comune extends DataClass {

    const TABLE_NAME = "COMUNI";

    public $ID = 0;
    public $CODICE_REGIONE = null;
    public $CD_PROVINCIA = null;
    public $CODICE_ISTAT = null;
    public $CODICE_PROVINCIA = null;
    public $PROV = null;
    public $COD_CF = null;
    public $DESCRIZIONE = null;
    public $ATTIVO = null;
    public $CAP = null;
    public $REGIONE = null;

    public function __construct($src = null) {
        global $con;
        if ($src == null)
            return;
        if (is_array($src)) {
            $this->_loadByRow($src, $stripSlashes);
        } elseif (is_numeric($src)) {
            // Carichiamo tramite ID
            $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE ID = :ID";
            $query = $con->prepare($sql);
            $query->bindParam(":ID", $src);
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
                Utils::FillObjectFromRow($this, $it[0]);
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }
    }

    public function LoadByIstat($CODICE_ISTAT) {
        global $con;
        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE CODICE_ISTAT = :CODICE_ISTAT";
        $query = $con->prepare($sql);
        $it = [];
        $query->bindParam(":CODICE_ISTAT", $CODICE_ISTAT);
        try {
            $query->execute();
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
            Utils::FillObjectFromRow($this, $it[0]);
        } catch (Exception $exc) {
            $exc->getMessage();
        }
        return $it;
    }

    public static function LoadByComune($comune = "") {
        global $con;
        $sql = "SELECT * FROM " . self::TABLE_NAME . " WHERE LOWER(DESCRIZIONE)=:comune";// OR  LOWER(DESCRIZIONE) = :comune
        $query = $con->prepare($sql);
        $query->bindParam(":comune", strtolower(trim($comune)));
        
        try {
            $query->execute();
            $it = $query->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $exc->getMessage();
        }
        return $it;
    }

    public static function getProvinciaFromComune($DESCRIZIONE = '') {
        global $con;
        $response = '';
        if ($DESCRIZIONE != '') {
            $sql = "SELECT DISTINCT(CODICE_PROVINCIA) as CODICE_PROVINCIA FROM " . self::TABLE_NAME . " WHERE DESCRIZIONE = :DESCRIZIONE";
            $query = $con->prepare($sql);
            $query->bindParam(":DESCRIZIONE", strtoupper($DESCRIZIONE));
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
                $response = $it[0]['CODICE_PROVINCIA'];
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }
        return $response;
    }

    public static function autocomplete($string = '', $regione = null) {
        global $con;
        $return = array();
        if (strlen($string) >= 3) {
            $filter = '';
            if (!empty($regione)){                
                $filter = " REGIONE = :regione AND ";
            }
            $string = "%" . strtolower($string) . "%";
            $sql = "SELECT DESCRIZIONE  , CODICE_ISTAT, CODICE_PROVINCIA,CAP FROM " . self::TABLE_NAME . " WHERE  " . $filter . " LOWER(DESCRIZIONE) LIKE :TERM ORDER BY DESCRIZIONE";
            $query = $con->prepare($sql,true);
            if (!empty($regione)){  
                $query->bindParam(":REGIONE", $regione);
            }
            $query->bindParam(":TERM", $string);
            try {
                $query->execute();
                while ($it = $query->fetch(PDO::FETCH_ASSOC)) {
                    $descrizione = $it['DESCRIZIONE'];
                    $row['id'] = $descrizione;
                    $row['text'] = $descrizione;
                    $row['label'] = $descrizione;
                    $row['codice_istat'] = $it['CODICE_ISTAT'];
                    $row['codice_provincia'] = $it['CODICE_PROVINCIA'];
                    $row['cap'] = $it['CAP'];
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

    public static function autocompleteSigla($string = '') {
        global $con;
        $return = array();
        $string = "%" . strtoupper($string) . "%";
        $sql = "SELECT DISTINCT(CODICE_PROVINCIA) as label FROM " . self::TABLE_NAME . " WHERE CODICE_PROVINCIA LIKE :term ORDER BY label";
        $query = $con->prepare($sql);
        $query->bindValue(":term", $string);
        try {
            $query->execute();
            $return = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            echo $exc->getMessage();
//                $return = "ERROR";
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
