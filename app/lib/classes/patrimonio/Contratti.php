<?php
require_once ROOT . "/lib/classes/IDataClass.php";
class Contratti extends DataClass
{
    const TABLE_NAME = "CONTRATTI";
    public $ID = 0;
    public $ID_PATRIMONIO = 0;
    public $ID_LOCATARIO = 0;
    public $NUMERO = '';
    public $DATA_CONTRATTO = null;
    public $TIPO_DURATA_CONTRATTO = 'Y';//( ANNUALE = Y ; MENSILE = M)
    public $DURATA_CONTRATTO = 0;
    public $TIPO_RATA = 'Y';//( ANNUALE = Y ; MENSILE = M)
    public $DATA_TERMINE = null;
    public $IMPORTO = 0;
    public $IMPORTO_RATA = 0;
    public $CANCELLATO = 0;
    public $locatario = array();


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
        $this->loadLocatario();
    }
    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }

    public static function Load($id_patrimonio = 0, $id_locatario = 0, $cancellato = 0) {
        global $con;
        $where = "";
        $order = " ORDER BY DATA_CONTRATTO";

        if (intval($id_patrimonio) > 0)
            $where .= ($where == "" ? "" : " AND ") . "ID_PATRIMONIO = :ID_PATRIMONIO";
        if (intval($id_locatario) > 0)
            $where .= ($where == "" ? "" : " AND ") . "ID_LOCATARIO = :ID_LOCATARIO";
        if (intval($cancellato) >= 0)
            $where .= ($where == "" ? "" : " AND ") . "CANCELLATO = :CANCELLATO";

        $sql = "SELECT * FROM " . self::TABLE_NAME;
        if ($where != "")
            $sql .= " WHERE $where $order";
        //ECHO $sql;EXIT();
        $query = $con->prepare($sql);
        if (intval($id_patrimonio) > 0)
            $query->bindParam(":ID_PATRIMONIO", intval($id_patrimonio));
        if (intval($id_locatario) > 0)
            $query->bindParam(":ID_LOCATARIO", intval($id_locatario));
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
    public function loadLocatario(){
        $this->locatario = new Locatari($this->ID_LOCATARIO);
    }

    public static function checkVerificaDatiPreSalvataggio() {
        global $LoggedAccount;

        $_POST['ID'] = intval(Utils::get_filter_int_POST('ID'));
        $_POST['ID_PATRIMONIO'] = intval(Utils::get_filter_int_POST('ID_PATRIMONIO'));
        $_POST['ID_LOCATARIO'] = intval(Utils::get_filter_int_POST('ID_LOCATARIO'));
        $_POST['DATA_CONTRATTO'] = Utils::get_filter_string_POST('DATA_CONTRATTO');
        $_POST['DATA_TERMINE'] = Utils::get_filter_string_POST('DATA_TERMINE');
        $_POST['IMPORTO'] = Utils::get_filter_string_POST('IMPORTO');
        $_POST['IMPORTO_RATA'] = Utils::get_filter_string_POST('IMPORTO_RATA');
        $_POST['TIPO_DURATA_CONTRATTO'] = Utils::get_filter_string_POST('TIPO_DURATA_CONTRATTO');
        $_POST['DURATA_CONTRATTO'] = Utils::get_filter_string_POST('DURATA_CONTRATTO');
        $_POST['TIPO_RATA'] = Utils::get_filter_string_POST('TIPO_RATA');
        $_POST['IMPORTO_RATA'] = Utils::get_filter_string_POST('IMPORTO_RATA');

    }

    public function checkDatiPreSave() {
        global $LoggedAccount;
        $response = array();

        $controllValidita = true;
        $dati_mancanti = '';

        if (trim($this->IMPORTO) <= 0) {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo importo </b> ";
        }
        if (trim($this->DATA_CONTRATTO) == '') {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo data dontratto</b> ";
        }
        if (intval($this->ID_LOCATARIO)==0) {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo locatario</b> ";
        }

        if (intval($this->ID_PATRIMONIO)==0) {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo patrimonio</b> ";
        }

        if (trim($this->TIPO_DURATA_CONTRATTO)== '') {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo tipo durata contratto</b> ";
        }
        if (intval($this->DURATA_CONTRATTO)== 0) {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo durata contratto</b> ";
        }

        if (trim($this->TIPO_RATA)== '') {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo tipo rata</b> ";
        }
        if (intval($this->IMPORTO_RATA)== 0) {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il importo della rata</b> ";
        }


        $checkDate = Date::checkSaveDate($this->DATA_CONTRATTO, $this->DATA_TERMINE);
        if ($checkDate['esito'] != 1) {
            $controllValidita = false;
            $dati_mancanti .= " <br>".$checkDate['erroreDescrizione'];
        }



        $response['checkDati'] = $controllValidita;
        $response['txt_dati_mancanti'] = $dati_mancanti;

        return $response;
    }

    public function addEvents($data){
        $response = Utils::initDefaultRowsResponse();
        $events = new Events();
        $events->START_DATE = $data;
        $events->END_DATE = $data;
        $events->ID_CONTRATTO = $this->ID;
        $events->TITLE = 'Contratto n. '.$this->NUMERO;
        $events->DESCRIPTION = 'Scadenza rata di €. '.number_format($this->IMPORTO_RATA, 2, ',','.');
        $response = $events->Save();
        return $response;
    }

    public function generaRate(){
        $response = Utils::initDefaultRowsResponse();
        $t = strtolower($this->TIPO_RATA);
        $data_inizio = new DateTime($this->DATA_CONTRATTO);
        $data_fine = new DateTime($this->DATA_TERMINE);
        //$n = Date::DateDiff($t, $data_inizio->format('d/m/Y'), $data_fine->format('d/m/Y'), true, true);
        $n = $data_inizio->diff($data_fine, true);
        $data_rata = new DateTime($this->DATA_CONTRATTO);
        $rate_salvate = 0;
        //echo $data_inizio->format('Y-m-d')."<br>";
        //echo $data_fine->format('Y-m-d');
        //Utils::print_array($n);

        //echo $n;
        //echo $n->format('%a');
        switch ($t){
            case 'y':
                $interval = abs(intval(($n->format('%a')/365)));
                break;
            case 'm':
                $interval = abs(intval($n->format('%a')/30));
                break;
        }
        //echo "---->".$interval;
        //exit();
        $reset = new Events();
        $reset->ID_CONTRATTO = $this->ID;
        $res = $reset->LogicalDeleteContratto();
        if($res['esito'] == 1) {
            for ($i = 0; $i < $interval; $i++) {
                $passo = "P1" . strtoupper($t);
                $data_rata->add(new DateInterval($passo));
                $res = $this->addEvents($data_rata->format('Y-m-d'));
                if ($res['esito'] == 1) {
                    $rate_salvate++;
                }
            }
        }
        //echo $rate_salvate ."==". $interval;
        if($rate_salvate == $interval){
            $response['esito']=1;
        }
        return $response;
    }

    public function generaScadenza(){
        $t = $this->TIPO_DURATA_CONTRATTO;
        $passo = "P".$this->DURATA_CONTRATTO.$t;
        $data_termine = new DateTime($this->DATA_CONTRATTO);
        $data_termine->add(new DateInterval($passo));
        $this->DATA_TERMINE = $data_termine->format('Y-m-d');
    }

    public function Save() {
        global $con;
        $vars = get_object_vars($this);
        unset($vars['locatario']);
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