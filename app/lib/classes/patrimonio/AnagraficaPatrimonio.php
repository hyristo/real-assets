<?php
require_once ROOT . "/lib/classes/IDataClass.php";

class AnagraficaPatrimonio extends DataClass
{
    const TABLE_NAME = "ANAG_PATRIMONIO";
    public $ID = 0;
    public $NOME = '';
    public $DESCRIZIONE = '';
    public $TIPO_PATRIMONIO = 0;
    public $INDIRIZZO = '';
    public $COMUNE = '';
    public $PROVINCIA = '';
    public $PARTICELLA = '';
    public $FOGLIO = '';
    public $SEZIONE = '';
    public $DIMENSIONI = '';
    public $STATO = 0;
    public $CANCELLATO = 0;
    public $foto= array();
    public $contratti = array();

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

        $this->foto = $this->loadFoto();
        $this->contratti = $this->loadContratti();
    }

    public function loadFoto() {
        return FotoPatrimonio::Load($this->ID);
    }

    public function loadContratti(){
        //echo "ci entro";exit();
        return Contratti::Load($this->ID);
    }

    public static function LoadDataTable($searchQuery = "", $searchArray = array(), $columnName = array(), $columnSortOrder = array(), $start = 0, $offset = 0) {
        return parent::_loadDataTable(self::TABLE_NAME, $searchQuery, $searchArray, $columnName, $columnSortOrder, $start, $offset);
    }

    public static function checkVerificaDatiPreSalvataggio() {
        global $LoggedAccount;

        $_POST['ID'] = intval(Utils::get_filter_int_POST('ID'));
        $_POST['NOME'] = Utils::get_filter_string_POST('NOME');
        $_POST['DESCRIZIONE'] = Utils::get_filter_string_POST('DESCRIZIONE');
        $_POST['TIPO_PATRIMONIO'] = Utils::get_filter_string_POST('TIPO_PATRIMONIO');
        $_POST['COMUNE'] = Utils::get_filter_string_POST('COMUNE');
        $_POST['PROVINCIA'] = Utils::get_filter_string_POST('PROVINCIA');
        $_POST['INDIRIZZO'] = Utils::get_filter_string_POST('INDIRIZZO');
        $_POST['CIVICO'] = Utils::get_filter_string_POST('CIVICO');
        $_POST['FOGLIO'] = Utils::get_filter_string_POST('FOGLIO');
        $_POST['PARTICELLA'] = Utils::get_filter_string_POST('PARTICELLA');
        $_POST['SEZIONE'] = Utils::get_filter_string_POST('SEZIONE');
        $_POST['DIMENSIONI'] = Utils::get_filter_string_POST('DIMENSIONI');
        $_POST['STATO'] = Utils::get_filter_string_POST('STATO');

    }

    public function checkDatiPreSave() {
        global $LoggedAccount;
        $response = array();

        $controllValidita = true;
        $dati_mancanti = '';

        if (trim($this->NOME) == '') {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo nome </b> ";
        }
        if (trim($this->DESCRIZIONE) == '') {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo descrizione</b> ";
        }
        if (intval($this->TIPO_PATRIMONIO)==0) {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo descrizione</b> ";
        }

        if (trim($this->COMUNE) == '') {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo comune </b> ";
        }
        if (trim($this->PROVINCIA) == '') {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo provincia </b> ";
        }
        if (trim($this->INDIRIZZO) == '') {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo indirizzo</b> ";
        }
        if (trim($this->FOGLIO) == '') {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo foglio</b> ";
        }
        if (trim($this->PARTICELLA) == '') {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo particella</b> ";
        }
        if (trim($this->SEZIONE) == '') {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo sezione</b> ";
        }
        if (trim($this->STATO) == '') {
            $controllValidita = false;
            $dati_mancanti .= " <br><b>Il campo stato</b> ";
        }


        $response['checkDati'] = $controllValidita;
        $response['txt_dati_mancanti'] = $dati_mancanti;

        return $response;
    }

    public function Save() {
        global $con;
        $vars = get_object_vars($this);
        unset($vars['foto']);
        unset($vars['contratti']);
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

