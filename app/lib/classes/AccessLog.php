<?php

/**
 * Description of UTENTI
 *
 * @author Gigi
 */
class AccessLog {

//put your code here
    const TABLE_NAME = "ACCESS_LOG";
    const SEQ_NAME = "ACCESS_LOG_ID_SEQ";

    public $ID = 0; // Primary key
    public $RESPONSE = "";
    public $FISCALNUMBER = "";
    public $INRESPONSETO = "";
    public $AUTHNINSTANT = "";
    public $AUTHENTICATINGAUTHORITY = "";
    public $REDIRECTTO = "";

    public function __construct($src = null) {
        global $conSpid;
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


    /*
     * verifica l'esistenza del record di login nella tabella ACCESS_LOG di SPID 
     */

    public static function selectLog($fiscalnumber = "", $inresponseto = "", $authnInstant = "", $redirectto = "") {
        global $conSpid;
        $return = false;
        $verify = $conSpid->prepare("SELECT ID FROM " . self::TABLE_NAME . " WHERE FISCALNUMBER =:fiscalnumber AND INRESPONSETO =:inresponseto AND "
                . "REDIRECTTO =:redirectto AND AUTHINSTANT =:authinstant");
        $verify->bindParam(":fiscalnumber", $fiscalnumber);
        $verify->bindParam(":inresponseto", $inresponseto);
        $verify->bindParam(":redirectto", $redirectto);
        $verify->bindParam(":authinstant", $authnInstant);
        try {
            $verify->execute();
            $itVerify = $verify->fetch(PDO::FETCH_ASSOC);
            $return = (intval($itVerify['ID']) > 0 ? true : false);
        } catch (Exception $exc) {}
        return $return;
    }

    public static function selectLogOCI($fiscalnumber = "", $inresponseto = "", $authnInstant = "", $redirectto = "") {
        global $conSpidOCI;
        $return = array();
        $return['esito'] = 1;
        $return['descrizioneErrore'] = "";
        $sql = "SELECT ID FROM " . self::TABLE_NAME . " WHERE FISCALNUMBER =:fiscalnumber AND INRESPONSETO =:inresponseto AND REDIRECTTO =:redirectto AND AUTHINSTANT =:authinstant";
        try {
            $statement = $conSpidOCI->db_getStatement($sql);
            oci_bind_by_name($statement, ":fiscalnumber", $fiscalnumber);
            oci_bind_by_name($statement, ":inresponseto", $inresponseto);
            oci_bind_by_name($statement, ":redirectto", $redirectto);
            oci_bind_by_name($statement, ":authinstant", $authnInstant);
            $row = $conSpidOCI->db_fetch_array($statement);
            $conSpidOCI->db_free_statement($statement);
            $conSpidOCI->db_close();
            if (intval($row['ID']) > 0) {
                $return['esito'] = 1;
            } else {
                $return['esito'] = 0;
            }
        } catch (Exception $exc) {
            $return['esito'] = -999;
            $return['descrizioneErrore'] = $exc->getMessage();
        }
        return $return;
    }

}
