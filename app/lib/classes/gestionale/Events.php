<?php
require_once ROOT . "lib/classes/IDataClass.php";

class Events extends DataClass
{
    const TABLE_NAME = "EVENTS";

    public $ID = 0;
    public $TITLE = "";
    public $ID_CONTRATTO = 0;
    public $DESCRIPTION = "";
    public $START_DATE = null;
    public $END_DATE = NULL;
    public $CREATED = NULL;
    public $CANCELLATO = 0;
    public $RATA_PAGATA = 0;

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

    public static function ListEventi($rata_pagata = 0, $title = '', $description = '', $cancellato = 0) {
        global $con;
        $where = "";
        $order = " ORDER BY E.START_DATE ASC";

        if (intval($rata_pagata) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "E.RATA_PAGATA = :RATA_PAGATA";
        if (intval($cancellato) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "E.CANCELLATO = :CANCELLATO";
        if (!empty($title))
            $where .= ($where == "" ? "" : " AND ") . "E.TITLE = :TITLE";
        if (!empty($description))
            $where .= ($where == "" ? "" : " AND ") . "E.DESCRIPTION = :DESCRIPTION";

        $sql = "SELECT E.*, C.ID_PATRIMONIO, C.ID_LOCATARIO, C.IMPORTO_RATA FROM " . self::TABLE_NAME. " E LEFT JOIN ".Contratti::TABLE_NAME." C on E.ID_CONTRATTO = C.ID " ;
        if ($where != "")
            $sql .= " WHERE $where $order";


        $query = $con->prepare($sql);
        if (intval($rata_pagata) >= 0)
            $query->bindParam(":RATA_PAGATA", intval($rata_pagata));
        if (intval($cancellato) >= 0)
            $query->bindParam(":CANCELLATO", intval($cancellato));
        if (!empty($title))
            $query->bindParam(":TITLE", $title);
        if (!empty($description))
            $query->bindParam(":DESCRIPTION", $description);
        try {
            $query->execute();
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }

    public static function ListRate($id_contratto = 0, $cancellato = 0) {
        global $con;
        $where = "";
        $order = " ORDER BY E.START_DATE ASC";

        if (intval($cancellato) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "E.CANCELLATO = :CANCELLATO";
        if (intval($id_contratto) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "E.ID_CONTRATTO = :CONTRATTO";

        $sql = "SELECT E.*, C.ID_PATRIMONIO, C.ID_LOCATARIO, C.IMPORTO_RATA FROM " . self::TABLE_NAME. " E LEFT JOIN ".Contratti::TABLE_NAME." C on E.ID_CONTRATTO = C.ID " ;
        if ($where != "")
            $sql .= " WHERE $where $order";


        $query = $con->prepare($sql);
        if (intval($cancellato) >= 0)
            $query->bindParam(":CANCELLATO", intval($cancellato));
        if (intval($id_contratto) >= 0)
            $query->bindParam(":CONTRATTO", $id_contratto);

        try {
            $query->execute();
            $it = $query->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            $it['esito'] = -999;
            $it['descrizioneErrore'] = $exc->getMessage();
        }
        return $it;
    }

    public function LoadDataTableCustom($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        $select = " E.*, C.ID_PATRIMONIO, C.ID_LOCATARIO, C.IMPORTO_RATA ";
        return parent::_loadDataTableEx($select, self::TABLE_NAME. " E LEFT JOIN ".Contratti::TABLE_NAME." C on E.ID_CONTRATTO = C.ID ", $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset, false);
    }


    public function Save() {
        global $con;
        $vars = get_object_vars($this);
        $vars['CREATED'] = date('Y-m-d');
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

    public function LogicalDelete() {
        return parent::_LogicalDelete(self::TABLE_NAME, ' ID = ' . $this->ID);
    }

    public function LogicalDeleteContratto() {
        return parent::_LogicalDelete(self::TABLE_NAME, ' ID_CONTRATTO = ' . $this->ID_CONTRATTO);
    }


}