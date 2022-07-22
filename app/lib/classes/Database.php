<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Database
 *
 * @author Anselmo
 */
class Database {

    //put your code here
    public $db = null;

    /**
     * Costruttore di classe
     */
    public function __construct($DBMS = "Mysql", $dbName = "", $params = "") {
        switch ($DBMS) {
            case "MySql":
                $this->db = new MySql($dbName, $params);
                break;
            case "SqlServer":
                //$this->db = new SqlServer($dbName, $params);
                break;
            case "MyDBMS":
                //$this->db = new MyDBMS($dbName, $params);
                break;
            case "PgSql":
                $this->db = new PgSql($dbName, $params);
                break;
            case "PgSqlSian":
                $this->db = new PgSqlSian($dbName, $params);
                break;
            case "PgSqlSafe":
                $this->db = new PgSqlSafe($dbName, $params);
                break;
            case "Oracle":
                $this->db = new Oracle($dbName, $params);
                break;
        }
    }

    /**
     * Restituisce l'oggetto connessione
     *
     * @return  Oggetto connessione
     */
    public function GetConnection() {
        return $this->db->_connection;
    }

}

class Oracle extends Database {

    private $_connection;
    private $_dbName;

    public function __construct($dbName, $params = "") {
        $this->_setConnection($dbName, $params);
    }

    private function _setConnection($dbName, $params) {

        if (!is_array($params)) {

            $this->_connection = false;
        } else {
            //echo "dbname:".$dbName;
            try {
                $conn = new PDO("oci:dbname=" . $params['DB_SERVER'], $params['DB_USER'], $params['DB_PASS']);
                //echo "<pre>".print_r($conn, true)."</pre>"; exit();
                if (!$conn)
                    $this->_connection = false;

                //$conn->exec("set names utf8");
                //$conn->exec("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YYYY HH24:MI:SS'");
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


                $this->_connection = $conn;
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
        }
        //$this->db = $this->_connection;
        //return $this->_connection;
    }

    public function getDbName() {
        return $this->_dbName;
    }

    public function prepare($sql) {

        return $this->_connection->prepare($sql);
    }

    public function lastInsertId($class) {
        $obj = new $class();
        $sql = "SELECT " . $obj::SEQ_NAME . ".CURRVAL AS LASTINSERTID FROM DUAL";
        $sh = $this->prepare($sql);
        try {
            $sh->execute();
            $it = $sh->fetch(PDO::FETCH_ASSOC);
            $ritorno = $it['LASTINSERTID'];
        } catch (PDOException $exc) {
// echo $exc->getMessage();
        }
        return $ritorno; //$this->_connection->lastInsertId();
    }

    public function db_transactionStart() {
        $res = true;
        if (!$this->db_inTransaction()) {
            $res = $this->_connection->beginTransaction(); //START TRANSACTION
        }
        return $res;
    }

    public function db_transactionCommit() {
        return $this->_connection->commit();
    }

    public function db_transactionRollback() {
        return $this->_connection->rollback();
    }

    public function db_inTransaction() {
        return $this->_connection->inTransaction();
    }

    public function db_errorInfo() {
        return $this->_connection->errorInfo();
    }

    public function disableAutocommmit() {
        try {
            $this->prepare("SET AUTOCOMMIT = OFF");
            $this->execute();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

}

class PgSql extends Database {

    private $_connection;
    private $_dbName;

    public function __construct($dbName, $params = "") {
        $this->_setConnection($dbName, $params);
    }

    private function _setConnection($dbName, $params) {

        if (!is_array($params)) {

            $this->_connection = false;
        } else {
            //echo "dbname:".$dbName;
            try {
                $conn = new PDO("pgsql:host=" . $params['DB_SERVER'] . ";dbname=$dbName", $params['DB_USER'], $params['DB_PASS']); //@odbc_connect($params['Connection'], $params['Username'], $params['Password']);
                //echo "<pre>".print_r($conn, true)."</pre>"; exit();
                if (!$conn)
                    $this->_connection = false;

                $conn->exec("set names utf8");
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->_connection = $conn;
            } catch (PDOException $e) {
                echo "Connection failed QDC: " . $e->getMessage();
            }
        }
        //$this->db = $this->_connection;
        //return $this->_connection;
    }

    public function getDbName() {
        return $this->_dbName;
    }

    public function prepare($sql, $strLower = true) {
        if ($strLower) {
            $sql = Utils::prepareSQLower($sql, ':', true);
        }
        return $this->_connection->prepare($sql);
    }

    public function lastInsertId() {

        return $this->_connection->lastInsertId();
    }

    public function db_transactionStart() {
        $res = true;
        if (!$this->db_inTransaction()) {
            $res = $this->_connection->beginTransaction(); //START TRANSACTION
        }
        return $res;
    }

    public function db_transactionCommit() {
        return $this->_connection->commit();
    }

    public function db_transactionRollback() {
        return $this->_connection->rollback();
    }

    public function db_inTransaction() {
        return $this->_connection->inTransaction();
    }

    public function db_errorInfo() {
        return $this->_connection->errorInfo();
    }

    public function disableAutocommmit() {
        try {
            $this->prepare("SET AUTOCOMMIT = OFF");
            $this->execute();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

}

class PgSqlSian extends Database {

    private $_connection;
    private $_dbName;

    public function __construct($dbName, $params = "") {
        $this->_setConnection($dbName, $params);
    }

    private function _setConnection($dbName, $params) {

        if (!is_array($params)) {

            $this->_connection = false;
        } else {
            //echo "dbname:".$dbName;
            try {
                $conn = new PDO("pgsql:host=" . $params['DB_SERVER'] . ";dbname=$dbName", $params['DB_USER'], $params['DB_PASS']); //@odbc_connect($params['Connection'], $params['Username'], $params['Password']);
                //echo "<pre>".print_r($conn, true)."</pre>"; exit();
                if (!$conn)
                    $this->_connection = false;

                $conn->exec("set names utf8");
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->_connection = $conn;
            } catch (PDOException $e) {
                //Utils::RedirectTo(BASE_HTTP."ops.php");
                echo "Connection failed SIAN: " . $e->getMessage();
                //exit();
            }
        }
        //$this->db = $this->_connection;
        //return $this->_connection;
    }

    public function getDbName() {
        return $this->_dbName;
    }

    public function prepare($sql) {
        return $this->_connection->prepare($sql);
    }

    public function lastInsertId() {

        return $this->_connection->lastInsertId();
    }

    public function db_transactionStart() {
        $res = true;
        if (!$this->db_inTransaction()) {
            $res = $this->_connection->beginTransaction(); //START TRANSACTION
        }
        return $res;
    }

    public function db_transactionCommit() {
        return $this->_connection->commit();
    }

    public function db_transactionRollback() {
        return $this->_connection->rollback();
    }

    public function db_inTransaction() {
        return $this->_connection->inTransaction();
    }

    public function db_errorInfo() {
        return $this->_connection->errorInfo();
    }

    public function disableAutocommmit() {
        try {
            $this->prepare("SET AUTOCOMMIT = OFF");
            $this->execute();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

}

class PgSqlSafe extends Database {

    private $_connection;
    private $_dbName;

    public function __construct($dbName, $params = "") {
        $this->_setConnection($dbName, $params);
    }

    private function _setConnection($dbName, $params) {

        if (!is_array($params)) {

            $this->_connection = false;
        } else {
            //echo "dbname:".$dbName;
            try {
                $conn = new PDO("pgsql:host=" . $params['DB_SERVER'] . ";dbname=$dbName", $params['DB_USER'], $params['DB_PASS']); //@odbc_connect($params['Connection'], $params['Username'], $params['Password']);
                //echo "<pre>".print_r($conn, true)."</pre>"; exit();
                if (!$conn)
                    $this->_connection = false;

                $conn->exec("set names utf8");
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->_connection = $conn;
            } catch (PDOException $e) {
                echo "Connection failed SAFE: " . $e->getMessage();
            }
        }
        //$this->db = $this->_connection;
        //return $this->_connection;
    }

    public function getDbName() {
        return $this->_dbName;
    }

    public function prepare($sql) {
        return $this->_connection->prepare($sql);
    }

    public function lastInsertId() {

        return $this->_connection->lastInsertId();
    }

    public function db_transactionStart() {
        $res = true;
        if (!$this->db_inTransaction()) {
            $res = $this->_connection->beginTransaction(); //START TRANSACTION
        }
        return $res;
    }

    public function db_transactionCommit() {
        return $this->_connection->commit();
    }

    public function db_transactionRollback() {
        return $this->_connection->rollback();
    }

    public function db_inTransaction() {
        return $this->_connection->inTransaction();
    }

    public function db_errorInfo() {
        return $this->_connection->errorInfo();
    }

    public function disableAutocommmit() {
        try {
            $this->prepare("SET AUTOCOMMIT = OFF");
            $this->execute();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

}

class MySql extends Database {

    private $_connection;
    private $_dbName;

    public function __construct($dbName, $params = "") {
        $this->_setConnection($dbName, $params);
    }

    private function _setConnection($dbName, $params) {

        if (!is_array($params)) {

            $this->_connection = false;
        } else {
            //echo "dbname:".$dbName;
            try {
                $conn = new PDO("mysql:host=" . $params['DB_SERVER'] . ";dbname=$dbName", $params['DB_USER'], $params['DB_PASS']); //@odbc_connect($params['Connection'], $params['Username'], $params['Password']);
                
                if (!$conn)
                    $this->_connection = false;

                $conn->exec("set names utf8");
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->_connection = $conn;
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
        }
        //$this->db = $this->_connection;
        //return $this->_connection;
    }

    public function getDbName() {
        return $this->_dbName;
    }

    public function prepare($sql) {

        return $this->_connection->prepare($sql);
    }

    public function lastInsertId() {

        return $this->_connection->lastInsertId();
    }

    public function db_transactionStart() {
        $res = true;
        if (!$this->db_inTransaction()) {
            $res = $this->_connection->beginTransaction(); //START TRANSACTION
        }
        return $res;
    }

    public function db_transactionCommit() {
        return $this->_connection->commit();
    }

    public function db_transactionRollback() {
        return $this->_connection->rollBack();
    }

    public function db_inTransaction() {
        return $this->_connection->inTransaction();
    }

    public function db_errorInfo() {
        return $this->_connection->errorInfo();
    }

    public function disableAutocommmit() {
        try {
            $this->prepare("SET AUTOCOMMIT = OFF");
            $this->execute();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }
    }

}

//echo DB_SERVER;
if (!isset($con)) {
    //$connessione = new Database(DBMS, DB_SERVER, array('DB_USER' => DB_USER, 'DB_PASS' => DB_PASS));
    $connessione = new Database(DBMS, DB_NAME, array('DB_SERVER' => DB_SERVER, 'DB_USER' => DB_USER, 'DB_PASS' => DB_PASS));
    $con = $connessione->db;
}
if (!isset($conSpid) && DBMS_SPID) {
    //$connessioneSpid = new Database(DBMS_SPID, DB_SERVER_SPID, array('DB_USER' => DB_USER_SPID, 'DB_PASS' => DB_PASS_SPID));
    $connessioneSpid = new Database(DBMS_SPID, DB_NAME_SPID, array('DB_SERVER' => DB_SERVER_SPID, 'DB_USER' => DB_USER_SPID, 'DB_PASS' => DB_PASS_SPID));
    $conSpid = $connessioneSpid->db;
}

if (!isset($conSian) && DBMS_SIAN) {
    $connessioneSian = new Database(DBMS_SIAN, DB_NAME_SIAN, array('DB_SERVER' => DB_SERVER_SIAN, 'DB_USER' => DB_USER_SIAN, 'DB_PASS' => DB_PASS_SIAN));
    $conSian = $connessioneSian->db;
}

if (!isset($conSafe) && DBMS_SAFE) {
    $connessioneSafe = new Database(DBMS_SAFE, DB_NAME_SAFE, array('DB_SERVER' => DB_SERVER_SAFE, 'DB_USER' => DB_USER_SAFE, 'DB_PASS' => DB_PASS_SAFE));
    $conSafe = $connessioneSafe->db;
}

//echo "<pre>".print_r($connessione->db, true)."</pre>"; exit();