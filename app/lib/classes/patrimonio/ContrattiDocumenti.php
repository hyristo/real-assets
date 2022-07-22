<?php

class ContrattiDocumenti extends DataClass
{
    const TABLE_NAME = "CONTRATTI_DOCUMENTI";
    public $ID = 0;
    public $ID_CONTRATTO = 0;
    public $DESCRIZIONE = '';
    public $PATH = '';
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
    }

    public static function Load($id_contratto = 0, $cancellato = 0) {
        global $con;
        $where = "";
        $order = " ORDER BY DESCRIZIONE";

        if (intval($id_contratto) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "ID_CONTRATTO = :ID_CONTRATTO";
        if (intval($cancellato) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "CANCELLATO = :CANCELLATO";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";

        $query = $con->prepare($sql);
        if (intval($id_contratto) >= 0)
            $query->bindParam(":ID_CONTRATTO", intval($id_contratto));
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



    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }

    public function Save() {
        global $con;
        $vars = get_object_vars($this);
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
     * @param string $basePath
     * @param string $destinationPath
     * @return string
     */
    public function generateDirForUpload($basePath = ROOT_UPLOAD, $destinationPath = '') {
        $path = '';
        if ($basePath != "") {
            $path = $basePath . $this->ID_CONTRATTO;
            File::createPath($path);
            if (is_dir($path) && !empty($destinationPath)) {
                $path .= "/" . $destinationPath;
                File::createPath($path);
            } else {
                $path = '';
            }
            if (!is_dir($path)) {
                $path = '';
            }
        }
        return $path;
    }

    public function deleteAllegato(){
        global $con, $LoggedAccount;
        $response = Utils::initDefaultResponse();
        if ($this->ID > 0){
            $fileNameTmp = $this->PATH;
            $response = $this->Delete();
            if ($response['esito'] == 1 && $fileNameTmp != "") {
                $filePath = $this->generateDirForUpload(ROOT_UPLOAD_DOCUMENTI, ROOT_UPLOAD_DOC);
                $fileSource = $filePath . "/" . $fileNameTmp;
                @unlink($fileSource);
                if (!file_exists($fileSource)) {
                    $response['esito'] = 1;
                } else {
                    $response = Utils::initDefaultResponse(-999, "Si è verificato un errore (1571) " . $fileSource);
                }

            }
        }
        return $response;
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