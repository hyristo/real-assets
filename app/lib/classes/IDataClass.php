<?php

require_once "Database.php";

/**
 * Interfaccia standard per classi legate a database
 */
interface IDataClass {

    public function Save();

    public function Delete();
}

/**
 * Classe di gestione standard per classi legate a database
 */
class DataClass implements IDataClass {

    protected function _loadAndFillObject($id = 0, $tableName, $src = null, $connection = null, $fieldPK = "ID") {
        $this->_loadById($id, $tableName, true, $connection, $fieldPK);
        Utils::FillObjectFromRow($this, $src); //, $stripSlashes
    }

    protected function _loadById($id = 0, $tableName, $fillObject = false, $connection = null, $fieldPK = "ID") {
        global $con, $LoggedAccount;
        if (!isset($con))
            return;

        if ($connection == null) {
            $connection = $con;
        }


        $response = null;
        if (!empty($id)) {
            $sql = "SELECT * FROM " . $tableName . " WHERE " . $fieldPK . " = :FIELDPK";

            $query = $connection->prepare($sql);
            $query->bindParam(":FIELDPK", $id);
            try {
                $query->execute();
                $it = $query->fetchAll(PDO::FETCH_ASSOC);
                $response = (!empty($it) ? $it[0] : array());
                if ($fillObject) {
                    Utils::FillObjectFromRow($this, $response);
                }
            } catch (Exception $exc) {
                $exc->getMessage();
            }
        }
        return $response;
    }

    /**
     * Inizializza l'oggetto corrente dall'array associativo specificato
     *
     * @param mixed $row Array associativo del record da caricare
     */
    protected function _loadByRow($row, $stripSlashes = false, $callbackOnExists = false) {

        Utils::FillObjectFromRow($this, $row, $stripSlashes, $callbackOnExists);
    }

    /**
     * Restituisce il totale dei record registrati
     *
     * @param string $filter Se specificato, filtra in funzione dei parametri specificati
     * @return int
     */
    protected static function _count($tableName, $filter = null, $connection = null) {
        global $con;

        if (!isset($con))
            return;

        if ($connection == null) {
            $connection = $con;
        }
        $count = 0;
        $sql = 'SELECT COUNT(*) as TOT FROM ' . $tableName;

        $group = false;
        if ($filter != null) {
            $sql .= " WHERE " . $filter;
            $group = strpos($filter, "GROUP BY");
        }
        //echo $sql;
        $query = $connection->prepare($sql, false);
        
        try {
            $query->execute();
            if ($group === false) {
                $rec = $query->fetch(PDO::FETCH_ASSOC);
            } else {
                $rows_fetch = $query->fetchAll(PDO::FETCH_ASSOC);                
                foreach ($rows_fetch as $row) {
                    $count++;
                }
                $rec['TOT'] = $count;
            }
        } catch (Exception $exc) {
            $exc->getMessage();
        }

        return $rec['TOT'];
    }

    protected static function _list($tableName, $returnedClass = "", $where = "", $order = null, $limit = null, $offset = null, &$count = null, $connection = null) {
        global $con;
        if ($connection == null) {
            $connection = $con;
        }
        return self::_listEx(null, $tableName, $returnedClass, $where, $order, $limit, $offset, $count, $connection);
    }

    protected static function _listEx($select, $from, $returnedClass = "", $where = "", $order = null, $limit = null, $offset = null, &$count = null, $connection = null) {
        global $con;
        if ($connection == null) {
            $connection = $con;
        }
        $typeOfConnection = get_class($connection);

        if ($count !== null) {
            $count = self::_count($from, $where, $connection);
        }
        if ($typeOfConnection == "PgSql") {
            $query = "SELECT " . ($select ? $select : "*") . " FROM $from";

            if ($where != "") {
                $query .= " WHERE " . $where;
            }
            if ($order != null) {
                $query .= " ORDER BY $order";
            }
            if ($limit != null && $limit > 0) {
                $query .= " LIMIT " . $limit . ($offset > 0 ? " OFFSET $offset " : "");
            }
        } elseif ($typeOfConnection == "Oracle") {
            $query = "SELECT " . ($select ? $select : "*") . " FROM $from";
            if ($where != "") {
                $query .= " WHERE " . $where;
            }
            if ($order != null) {
                $query .= " ORDER BY $order";
            }
        }
        return self::_loadQuery($query, $returnedClass, $connection);
    }

    protected static function _loadQuery($query, $returnedClass = "", $connection = null) {
        global $con;
        if (!isset($con) && $connection == null) {
            return;
        }
        if ($connection == null) {
            $connection = $con;
        }
        $rows = array();

        $res = $connection->prepare($query);
        if ($res->execute()) {
            $rows_fetch = $res->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows_fetch as $row) {
                $keys = array_keys($row);
                foreach ($keys as $k) {
                    $row[$k] = utf8_encode($row[$k]);
                }
                $rows[] = ($returnedClass == "" ? $row : new $returnedClass($row));
            }
        }
        return $rows;
    }

    /**
     * Salva su database l'oggetto corrente e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function Save() {
        
    }

    protected function _Save($query = null, $connection = null) {
        global $con, $LoggedAccount, $bando_setup;
        $c_con = $con;
        if ($connection) {
            $c_con = $connection;
        }
        $class = get_class($this);
        $return = array();
        $action = "";
        //echo "<pre>". print_r($_SESSION, true)."</pre>";
        /**
         * INSERIRE I CONTROLLI SUI PERMESSI
         */
//        $return['esito'] = -999;
//        $return['descrizioneErrore'] = 'Non hai i permessi per procedere con il salvataggio';         
//        return $return;
//        exit();
        preg_match('/(INSERT)/', strtoupper($query->queryString), $matchesInsert);
        preg_match('/(DELETE)/', strtoupper($query->queryString), $matchesDelete);
        preg_match('/(UPDATE)/', strtoupper($query->queryString), $matchesUpdate);
        preg_match('/(CALL)/', strtoupper($query->queryString), $matchesStored);
        if ($matchesInsert) {
            $action = $matchesInsert[0];
        } elseif ($matchesDelete) {
            $action = $matchesDelete[0];
        } elseif ($matchesUpdate) {
            $action = $matchesUpdate[0];
        } elseif ($matchesStored) {
            $action = $matchesStored[0];
        }

        if ($this->ID > 0) {
            $objectValue = new $class($this->ID);
            $oldValue = json_encode($objectValue);
        } else {
            $oldValue = null;
        }
        //echo "<br>".$query->queryString."<br>";
        if ($query) {
            //$query->debugDumpParams();
            try {
                if ($query->execute()) {
                    if ($action == 'INSERT' && isset($this->ID)) {

                        $lastId = $c_con->lastInsertId($class);
                        $this->ID = $lastId;
                    } elseif ($action == 'INSERT' && !isset($this->ID)) {
                        $lastId = 0;
                    } elseif ($action == 'CALL') {
                        if ($this->ID > 0) {
                            $action = 'UPDATE';
                            $lastId = $this->ID;
                        } else {
                            $action = 'INSERT';
                            $lastId = $c_con->lastInsertId($class);
                        }
                    } else {
                        $lastId = $this->ID;
                    }

                    if (!in_array($class, $GLOBALS["MODULI_NO_LOGS"])) {
                        $logs = new Logs();
                        $logs->ID_OPERATORE = $LoggedAccount->ID;
                        $logs->IP = $_SERVER['REMOTE_ADDR'];
                        $logs->USERNAME = $LoggedAccount->CODICE_FISCALE;
                        $logs->MODULE = get_class($this);
                        $logs->ACTION = $action;
                        $logs->OPERATION = json_encode($query->queryString);
                        $logs->INFO = json_encode($this);
                        $logs->OLDVALUE = $oldValue;
                        $return['saveLogs'] = $logs->Save();
                    }

                    $return['action'] = $action;
                    $return['lastId'] = $lastId;
                    $return['esito'] = 1;
                }
            } catch (Exception $exc) {
                //echo print_r($exc);
                //$query->debugDumpParams();
                //echo  $exc->getMessage();exit();
                $return['esito'] = -999;
                $return['erroreDescrizione'] = $exc->getMessage();
            }
        }

        return $return;
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function Delete() {
        
    }

    protected function _Delete($tableName, $filter = null, $connection = null) {
        global $con, $LoggedAccount;

        if ($connection == null)
            $connection = $con;

        $class = get_class($this);
        if ($this->ID > 0) {
            $objectValue = new $class($this->ID);
            $oldValue = json_encode($objectValue);
        } else
            $oldValue = "";


        if ($filter) {
            $sql = "DELETE FROM $tableName WHERE $filter";
            $query = $connection->prepare($sql);
            if ($query->execute()) {
                try {
                    if (!in_array($class, $GLOBALS["MODULI_NO_LOGS"])) {
                        $logs = new Logs();
                        $logs->ID_OPERATORE = $LoggedAccount->ID;
                        $logs->IP = $_SERVER['REMOTE_ADDR'];
                        $logs->USERNAME = $LoggedAccount->CODICE_FISCALE;
                        $logs->MODULE = get_class($this);
                        $logs->ACTION = 'DELETE';
                        $logs->OPERATION = $query->queryString;
                        $logs->INFO = json_encode($this);
                        $logs->OLDVALUE = $oldValue;
                        $return['saveLogs'] = $logs->Save();
                    }
                    $return['esito'] = 1;
                } catch (Exception $exc) {
//                $query->debugDumpParams();
                    $return['esito'] = -999;
                    $return['erroreDescrizione'] = $exc->getMessage();
                }
            }
        }
        return $return;
    }

    /**
     * Elimina l'oggetto corrente dal database e restituisce TRUE se ha successo
     *
     * @return bool Risultato booleano dell'operazione
     */
    public function LogicalDelete() {
        
    }

    protected function _LogicalDelete($tableName, $filter = null, $connection = null) {
        global $con, $LoggedAccount;
        if ($connection == null)
            $connection = $con;
        $return = array();
        $class = get_class($this);

        if ($this->ID > 0) {
            $objectValue = new $class($this->ID);
            $oldValue = json_encode($objectValue);
        } else
            $oldValue = "";
        if ($filter) {
            $sql = 'UPDATE ' . $tableName . '  SET CANCELLATO = 1 WHERE 1 = 1 AND ' . $filter;
            $query = $con->prepare($sql, false);
            if ($query->execute()) {
                try {
                    if (!in_array($class, $GLOBALS["MODULI_NO_LOGS"])) {
                        $logs = new Logs();
                        $logs->ID_OPERATORE = $LoggedAccount->ID;
                        $logs->IP = $_SERVER['REMOTE_ADDR'];
                        $logs->USERNAME = $LoggedAccount->ID;
                        $logs->MODULE = get_class($this);
                        $logs->ACTION = "LOGICAL DELETE";
                        $logs->OPERATION = json_encode($query->queryString);
                        $logs->INFO = json_encode($this);
                        $logs->OLDVALUE = $oldValue;
                        $return['saveLogs'] = $logs->Save();
                    }
                    $return['esito'] = 1;
                } catch (Exception $exc) {
                    $query->debugDumpParams();
                    $return['esito'] = -999;
                    $return['descrizioneErrore'] = $exc->getMessage();
                }
            }
        }
        return $return;
    }

    /**
     * Funzione da utilizzare esclusivamente per la gestione della DataTable (js)
     * @global type $con connessione al databases
     * @global type $Databases classe di riferimento al db per stabilire che tipo di db si sta utilizzando
     * @param type $DbTable nome della tabella del db
     * @param type $searchQuery stringa sql per la ricerca
     * @param type $searchArray array con i nomi delle colonne su cui fare la ricerca
     * @param type $columnName nome delle colonne per l'order by
     * @param type $columnSortOrder ordinamento 
     * @param type $start 
     * @param type $offset
     * @return type array()
     */
    protected static function _loadDataTable($DbTable = "", $searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0, $totCount = true) {
        //echo $searchQuery;
        return self::_loadDataTableEx("*", $DbTable, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset, $totCount);
    }

    protected static function _loadDataTableEx($field = '*', $DbTable = "", $searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0, $totCount = true, $connection = null) {
        global $con;
        if (!empty($connection)) {
            $typeOfConnection = get_class($connection);
            $con = $connection;
        } else {
            $typeOfConnection = get_class($con);
        }


        $response = array();

        if ($typeOfConnection == "PgSql") {


            $totalRecords = 0;
            $totalRecordwithFilter = 0;
            ## Total number of records without filtering
            //echo $searchQuery;
            $sqlAllCount = "SELECT COUNT(*) AS allcount FROM " . $DbTable . " WHERE 1 = 1 " . $searchQuery;
            //echo $sqlAllCount;
            $queryAllCount = $con->prepare($sqlAllCount, true);
            
            // Bind values

            foreach ($searchArray as $key => $search) {
                $queryAllCount->bindValue(':' . $key, $search, PDO::PARAM_STR);
            }
            $queryAllCount->execute();
            $recordsAllCount = $queryAllCount->fetch();
            $totalRecords = $recordsAllCount['ALLCOUNT'];


            ## Total number of records with filtering
            $sqlAllCountFiltering = "SELECT COUNT(*) AS allcount FROM " . $DbTable . " WHERE 1 = 1 " . $searchQuery;
            $queryAllCountFiltering = $con->prepare($sqlAllCountFiltering, true);
            // Bind values

            foreach ($searchArray as $key => $search) {
                $queryAllCountFiltering->bindValue(':' . $key, $search, PDO::PARAM_STR);
            }
            $queryAllCountFiltering->execute($searchArray);
            $recordsAllCountFiltering = $queryAllCountFiltering->fetch();
            $totalRecordwithFilter = $recordsAllCountFiltering['ALLCOUNT'];

            ## Fetch records
            $sqlRecords = "SELECT " . $field . " FROM " . $DbTable . " WHERE 1 = 1  " . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit OFFSET :offset";
            $queryRecords = $con->prepare($sqlRecords, true);

            // Bind values
            foreach ($searchArray as $key => $search) {
                $queryRecords->bindValue(':' . $key, $search, PDO::PARAM_STR);
            }

            $queryRecords->bindValue(':LIMIT', (int) $offset, PDO::PARAM_INT);
            $queryRecords->bindValue(':OFFSET', (int) $start, PDO::PARAM_INT);
//            $queryRecords->bindValue(':limit',  $offset);
//            $queryRecords->bindValue(':offset',  $start);
            $queryRecords->execute();
            $empRecords = $queryRecords->fetchAll(PDO::FETCH_ASSOC);


            ## Response
            $response = array(
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordwithFilter,
                "empRecords" => $empRecords
            );
        } elseif ($typeOfConnection == "PgSqlSian") {


            $totalRecords = 0;
            $totalRecordwithFilter = 0;
            ## Total number of records without filtering
            $sqlAllCount = "SELECT COUNT(*) AS allcount FROM " . $DbTable . " WHERE 1 = 1 " . $searchQuery;
            $queryAllCount = $con->prepare($sqlAllCount);

            // Bind values
            foreach ($searchArray as $key => $search) {
                $queryAllCount->bindValue(':' . $key, $search, PDO::PARAM_STR);
            }

            $queryAllCount->execute();
            $recordsAllCount = $queryAllCount->fetch();
            $totalRecords = $recordsAllCount['allcount'];


            ## Total number of records with filtering
            $sqlAllCountFiltering = "SELECT COUNT(*) AS allcount FROM " . $DbTable . " WHERE 1 = 1 " . $searchQuery;
            $queryAllCountFiltering = $con->prepare($sqlAllCountFiltering);
            // Bind values

            foreach ($searchArray as $key => $search) {
                $queryAllCountFiltering->bindValue(':' . $key, $search, PDO::PARAM_STR);
            }
            $queryAllCountFiltering->execute($searchArray);
            $recordsAllCountFiltering = $queryAllCountFiltering->fetch();
            $totalRecordwithFilter = $recordsAllCountFiltering['allcount'];

            ## Fetch records
            $sqlRecords = "SELECT " . $field . " FROM " . $DbTable . " WHERE 1 = 1  " . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit OFFSET :offset";
            $queryRecords = $con->prepare($sqlRecords);
//            Utils::print_array($searchArray); 
//            echo $sqlRecords; exit();
            // Bind values
            foreach ($searchArray as $key => $search) {
                $queryRecords->bindValue(':' . $key, $search, PDO::PARAM_STR);
            }

            $queryRecords->bindValue(':limit', (int) $offset, PDO::PARAM_INT);
            $queryRecords->bindValue(':offset', (int) $start, PDO::PARAM_INT);
//            $queryRecords->bindValue(':limit',  $offset);
//            $queryRecords->bindValue(':offset',  $start);
            $queryRecords->execute();
            $empRecords = $queryRecords->fetchAll(PDO::FETCH_ASSOC);


            ## Response
            $response = array(
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordwithFilter,
                "empRecords" => $empRecords
            );
        } elseif ($typeOfConnection == "MySql") {


            $totalRecords = 0;
            $totalRecordwithFilter = 0;
            ## Total number of records without filtering
            $sqlAllCount = "SELECT COUNT(*) AS allcount FROM " . $DbTable . " WHERE 1 = 1 " . $searchQuery;
            $queryAllCount = $con->prepare($sqlAllCount);

            // Bind values
            foreach ($searchArray as $key => $search) {
                $queryAllCount->bindValue(':' . $key, $search, PDO::PARAM_STR);
            }

            $queryAllCount->execute();
            $recordsAllCount = $queryAllCount->fetch();
            $totalRecords = $recordsAllCount['allcount'];


            ## Total number of records with filtering
            $sqlAllCountFiltering = "SELECT COUNT(*) AS allcount FROM " . $DbTable . " WHERE 1 = 1 " . $searchQuery;
            $queryAllCountFiltering = $con->prepare($sqlAllCountFiltering);
            // Bind values

            foreach ($searchArray as $key => $search) {
                $queryAllCountFiltering->bindValue(':' . $key, $search, PDO::PARAM_STR);
            }
            $queryAllCountFiltering->execute($searchArray);
            $recordsAllCountFiltering = $queryAllCountFiltering->fetch();
            $totalRecordwithFilter = $recordsAllCountFiltering['allcount'];

            ## Fetch records
            $sqlRecords = "SELECT " . $field . " FROM " . $DbTable . " WHERE 1 = 1  " . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit OFFSET :offset";
            $queryRecords = $con->prepare($sqlRecords);
//            Utils::print_array($searchArray); 
//            echo $sqlRecords; exit();
            // Bind values
            foreach ($searchArray as $key => $search) {
                $queryRecords->bindValue(':' . $key, $search, PDO::PARAM_STR);
            }

            $queryRecords->bindValue(':limit', (int) $offset, PDO::PARAM_INT);
            $queryRecords->bindValue(':offset', (int) $start, PDO::PARAM_INT);
//            $queryRecords->bindValue(':limit',  $offset);
//            $queryRecords->bindValue(':offset',  $start);
            $queryRecords->execute();
            $empRecords = $queryRecords->fetchAll(PDO::FETCH_ASSOC);


            ## Response
            $response = array(
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordwithFilter,
                "empRecords" => $empRecords
            );
        } elseif ($typeOfConnection == "Oracle") {
            $totalRecords = 0;
            $totalRecordwithFilter = 0;

            if ($totCount) {
                ## Total number of records without filtering
                $sqlAllCount = "SELECT COUNT(*) as ALLCOUNT FROM (" . $DbTable . ") WHERE 1 = 1 ";
                //$sqlAllCount = "SELECT * FROM " . $DbTable;
                $queryAllCount = $con->prepare($sqlAllCount);

                // Bind values
                foreach ($searchArray as $key => $search) {
                    $queryAllCount->bindValue(':' . $key, $search, PDO::PARAM_STR);
                }
                $queryAllCount->execute();
                $recordsAllCount = $queryAllCount->fetch();
//            echo "<pre>".print_r($recordsAllCount, true)."</pre>";
                //$queryAllCount->debugDumpParams();
                $totalRecords = $recordsAllCount['ALLCOUNT'];
//            echo $recordsAllCount['ALLCOUNT']."################";
            }
//            
            ## Total number of records with filtering
            $sqlAllCountFiltering = "SELECT COUNT(*) AS ALLCOUNT FROM (" . $DbTable . ") WHERE 1 = 1 " . $searchQuery;
            $queryAllCountFiltering = $con->prepare($sqlAllCountFiltering);
//            exit($sqlAllCountFiltering);
            // Bind values
            foreach ($searchArray as $key => $search) {
                $queryAllCountFiltering->bindValue(':' . $key, $search, PDO::PARAM_STR);
            }
            //EXIT(Utils::print_array($searchArray));
            $queryAllCountFiltering->execute($searchArray);
            $recordsAllCountFiltering = $queryAllCountFiltering->fetch();
            $totalRecordwithFilter = $recordsAllCountFiltering['ALLCOUNT'];
            /**/
            if (!$totCount) {
                $totalRecords = $totalRecordwithFilter;
            }

            ## Fetch records

            $sqlRecords = "SELECT T.* FROM ( SELECT T.*, rowNum as rowIndex FROM ( SELECT " . $field . " FROM (" . $DbTable . ") WHERE 1 = 1  " . $searchQuery;
            if ($columnName != "") {
                $sqlRecords .= " ORDER BY " . $columnName . " " . $columnSortOrder . "";
            }
            $sqlRecords .= " )T)T WHERE rowIndex > :limit AND rowIndex <= :offset";


            $queryRecords = $con->prepare($sqlRecords);

            // Bind values
            foreach ($searchArray as $key => $search) {
                $queryRecords->bindValue(':' . $key, $search, PDO::PARAM_STR);
            }


//            echo $start."<---->".(int) ($start + $offset);
            $queryRecords->bindValue(':limit', (int) $start, PDO::PARAM_INT);
            $queryRecords->bindValue(':offset', (int) ($start + $offset), PDO::PARAM_INT);
//            var_dump($queryRecords);


            $queryRecords->execute();
            $j = 0;
            foreach ($cblobColumn as $v) {
                $j++;
                $queryRecords->bindColumn($j, $v, PDO::PARAM_LOB);
            }
            if (count($cblobColumn) > 0) {
                $empRecords = array();
                $i = 0;
                while ($it = $queryRecords->fetch(PDO::FETCH_ASSOC)) {
                    foreach ($cblobColumn as $v) {
                        $it[$v] = html_entity_decode(htmlspecialchars_decode(($it[$v])));
                    }
                    array_push($empRecords, $it);
                }
            } else {
                $empRecords = $queryRecords->fetchAll(PDO::FETCH_ASSOC);
            }


            //var_dump($empRecords);
            ## Response
            $response = array(
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordwithFilter,
                "empRecords" => $empRecords
            );
        }
        return $response;
    }

}
