<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Utils
 *
 * @author Anselmo
 */
class Utils {

    public static function extractGenderFromCF($codiceFiscale = '') {
        $response = "";
        if (!empty($codiceFiscale)) {
            $sesso = intval((substr($codiceFiscale, 9, 2)));
            $response = ($sesso <= 31 ? 'M' : 'F');
        }
        return $response;
    }

    public static function getIntestazioniFromSexByCF($codiceFiscale = '') {
        $response = array(
            'sesso' => '',
            'sottoscritto' => '',
            'natoa' => ''
        );
        if (!empty($codiceFiscale)) {
            $sesso = intval((substr($codiceFiscale, 9, 2)));
            $response = array(
                'sesso' => ($sesso <= 31 ? 'M ' : 'F'),
                'sottoscritto' => ($sesso <= 31 ? 'Il sottoscritto ' : 'La sottoscritta '),
                'natoa' => ($sesso <= 31 ? 'nato a ' : 'nata a ')
            );
        }
        return $response;
    }

    public static function checkLoginCredentials($username = '', $password = '') {
        $response['esito'] = -999;
        if (!EmailSms::checkEmail($username)) {
            $response['erroreDescrizione'] = "Username/Email non valida";
        } else if (!Utils::checkPassword($password)) {
            $response['erroreDescrizione'] = "Verificare la password inserita";
        } else {
            $response['esito'] = 1;
        }
//        Utils::print_array($response);
        return $response;
    }

    public static function getDecodedField($value = "") {
        return html_entity_decode(htmlspecialchars_decode($value));
    }

    public static function getStreamField($value = "") {
        return html_entity_decode(htmlspecialchars_decode(stream_get_contents($value)));
    }

    public static function checkPassword($password = "") {
        return (!empty($password) && strlen($password) >= MIN_PASSWORD_LENGHT ? true : false);
    }

    public static function checkLogin() {
        if (!isset($_SESSION['ID'])) {
            header("Location: " . PATH_LOGIN);
        }
    }

    public static function checkLoginWS() {
        return isset($_SESSION['ID']);
    }

    /**
     * Controllo se ho impostata la sessione per poter accedere ai servizi
     * @return boolean
     */
    public static function canAccess() {
        $access = false;
        if (isset($_SESSION['ID']) && intval($_SESSION['ID']) > 0) {
            $access = true;
        }
        return $access;
    }

    function stringEndsWith($string, $endString) {
        $len = strlen($endString);
        if ($len == 0) {
            return true;
        }
        return (substr($string, -$len) === $endString);
    }

    /*
     * Normalize default value for type & check field funzione     
     */

    public static function getArrayValueOrNull($array, $value) {
        return (!isset($array[$value]) ? NULL : $array[$value]);
    }

    public static function normalizeDefaultValueTypeString($value) {
        return $value == "" ? NULL : $value;
    }

    public static function normalizeDefaultValueTypeNumber($value) {
        return $value == "" ? 0 : $value;
    }

    public static function normalizeDefaultValueTypeDate($value, $format = "Y-m-d") {
        if (empty($value)) {
            $value = NULL;
        } else {
            $value = strtotime($value);
            $value = date($format, $value);
        }
        return $value;
    }

    public static function isNull($value) {
        return !empty($value) ? false : true;
    }

    public static function getDateField($key, $value) {
        $pos = strpos($key, "data_");
        if (($pos !== false) && ($value == "")) {
            $value = NULL;
        }
        return $value;
    }

    public static function getFromReq($param, $defval = null) {
        return (isset($_REQUEST[$param]) ? trim($_REQUEST[$param]) : $defval);
    }

    public static function getCheckInputIntValue($inputArray = array(), $field = '') {
        return (isset($inputArray[$field]) && strtolower($_REQUEST[$field]) == 'on' ? 1 : 0);
    }

    /*
     * Funzione per il controllo dei dati ricevuti 
     * 
     * arrayString serve nel caso in cui si vuole bypassare un controllo di un campo numerico
     * 
     */

    public static function requestDati($array = array(), $arrayString = array()) {
        $lunghezza = count($array);
        $ritorno = array();
        foreach ($array as $key => $element) {

            $nuovoValorNumeric = "";
            $nuovoValorString = "";
            if (is_numeric($element) && substr($element, 0, 1) != '0' && (!in_array($key, $arrayString))) {
                $nuovoValorNumeric = trim(self::get_filter_int($element));
                $ritorno[$key] = $nuovoValorNumeric;
            } else {
                if ($key != "action" && $key != "module" && $key != "password") {
                    $nuovoValorString = trim((Utils::get_filter_string($element)));
                    $ritorno[$key] = $nuovoValorString;
                }
//                if ($key == "username" || $key == "USERNAME" || $key=="NOME_UTENTE") {
//                    $nuovoValorString = trim(strtolower(Utils::get_filter_string_No_Input($element)));
//                    $ritorno[$key] = $nuovoValorString;
//                }
            }
        }
        return $ritorno;
    }

    /**
     * VALIDATEINPUT
     * @param type $arrayPost Array post
     * @param type $arraycontrollo Passaggio di un array Associativo con la Relativa lunghezza da Controllare
     * @return type
     */
    public static function validateInput($arrayPost = array(), $arraycontrollo = array()) {
        $booleanVerify = true;
        foreach ($arraycontrollo as $key => $value) {
            foreach ($arrayPost as $keyDue => $element) {
                if (($keyDue == $key) && (strlen($element) > $value)) {
                    $booleanVerify = false;
                    return $booleanVerify;
                }
            }
        }
        return $booleanVerify;
    }

    /*     * ***********************
     * controlli sulle stringe
     * *********************** */

    public static function get_filter_string64_get($valore) {
        $options = array('options' => array('default' => NULL));
        $valid = filter_input(INPUT_GET, $valore, FILTER_SANITIZE_STRING, $options);
        return base64_decode($valid); // Default will return
    }

    public static function get_filter_string64($valore) {
        $options = array('options' => array('default' => NULL));
        $valid = filter_input(INPUT_POST, $valore, FILTER_SANITIZE_STRING, $options);
        return base64_decode($valid); // Default will return
    }

    public static function get_filter_int_POST($valore) {
        $options = array('options' => array('default' => NULL));
        $valid = filter_input(INPUT_POST, $valore, FILTER_VALIDATE_INT, $options);
        if ($valid == "") {
            $valid = NULL;
        }
        return $valid; // Default will return
    }

    /*
     * Restituisce i valori da una funzione non da input
     */

    public static function get_filter_int($valore) {
        $options = array('options' => array('default' => NULL, "min_range" => 0, 'max_range' => PHP_INT_MAX));
        $valid = filter_var($valore, FILTER_VALIDATE_INT, $options);
        if ($valid == "") {
            $valid = NULL;
        }
        return $valid; // Default will return
    }

    public static function get_filter_string($valore) {
        $options = array('options' => array('default' => NULL));
        $valid = filter_var($valore, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        if ($valid == "") {
            $valid = NULL;
        }
        return $valid; // Default will return
    }

    /**
     * Filtra gli input stringa $_POST 
     * @param type $valore; se $post è true $valore deve essere il nome della chiave POST altrimenti deve essere la variabile 
     * @param type $post; true = gestisce il INPUT_POST; false gestice il valore della variabile $valore
     * @return type
     */
    public static function get_filter_string_POST($valore, $array = false) {
        $options = array('options' => array('default' => NULL));
        if (!$array) {
            $valid = filter_input(INPUT_POST, $valore, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES);
        } else {
            $valid = filter_input(INPUT_POST, $valore, FILTER_SANITIZE_STRING, FILTER_REQUIRE_ARRAY);
        }
        if ($valid == "") {
            $valid = NULL;
        }
        return $valid; // Default will return
    }

    /* public static function get_filter_array_string($valore) {
      $options = array($valore => array(
      'filter' => FILTER_VALIDATE_REGEXP | FILTER_SANITIZE_STRING,
      'flags' => FILTER_FLAG_STRIP_HIGH | FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_BACKTICK,
      'options' => array('regexp' => "/t(.*)/"),
      ));
      $valid = filter_input_array(INPUT_POST, $options);
      if ($valid == "") {
      $valid = NULL;
      }
      return $valid; // Default will return
      } */

    public static function is_base64_encoded($data) {
//        if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $data)) {
        if ($data === base64_encode(base64_decode($data))) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /*
     * decodifica il valora dalla string base64 (se codificato) 
     */

    public static function decodeBase64value($field = '') {
        $response = '';
        if ($field != '' && self::is_base64_encoded($field)) {
            $response = base64_decode($field);
        } else {
            $response = $field;
        }
        return $response;
    }

    public static function generateCodice($length = 8) {
        $password = '';
        $possibleChars = '0123456789';
        $i = 0;
        while ($i < $length) {
            $char = substr($possibleChars, mt_rand(0, strlen($possibleChars) - 1), 1);
            if (!strstr($password, $char)) {
                $password .= $char;
                $i++;
            }
        }
        return $password;
    }

    public static function generatePassword($length = 8) {
        $password = '';
        $possibleChars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@!.';
        $i = 0;
        while ($i < $length) {
            $char = substr($possibleChars, mt_rand(0, strlen($possibleChars) - 1), 1);
            if (!strstr($password, $char)) {
                $password .= $char;
                $i++;
            }
        }
        return $password;
    }

    public static function getStrReplace($param = "") {
        $param = str_replace(".", ",", $param);
        return $param;
    }

    /**
     * Tronca il testo eliminando tutti i tag html contenuti in esso.
     * @param mixed $testo This il testo da troncare
     * @param mixed $caratteri This il numero dei catarreri da rstituire
     * 
     * @return mixed Restituisce il testo troncato escluso anche dai tag html
     * 
     * */
    public static function troncaTesto($testo, $caratteri = 50) {
        $testo = strip_tags($testo);
        if (strlen($testo) <= $caratteri)
            return $testo;
        $nuovo = wordwrap($testo, $caratteri, "|");
        $nuovotesto = explode("|", $nuovo);
        return $nuovotesto[0] . "...";
    }

    /**
     * Inizializza le proprieta' di un oggetto con i valori di un array associativo
     *
     * @param mixed $obj L'oggetto da inizializzare, deve essere passato by-ref
     * @param array $row L'array associativo da cui recuperare i valori
     * @param mixed $callbackOnExists (Facoltativo) Metodo dell'oggetto da richiamare quando si deve assegnare il valore alla proprieta'
     */
    public static function FillObjectFromRow(&$obj, $row, $stripSlashes = false, $callbackOnExists = false) {
        $props = get_class_vars(get_class($obj));
        foreach ($props as $prop => $value) {
            if ($row != null && array_key_exists($prop, $row)) {
                if (!$callbackOnExists)
                    $obj->$prop = ($stripSlashes ? stripslashes($row[$prop]) : $row[$prop]);
                else
                    $obj->$callbackOnExists($prop, utf8_encode($row[$prop]));
            }
        }
    }

    public static function prepareKeyArray(&$src) {
        if (is_array($src)) {
            foreach ($src as $k => $v) {
                $kn = str_replace('-', '', $k);
                $newsrc[$kn] = Utils::prepareKeyArray($v);
            }
        } else {
            $newsrc = $src;
        }
        return $newsrc;
    }

    public static function InsertArrayIndex($array, $new_element, $index) {
        /*         * * get the start of the array ** */
        $start = array_slice($array, 0, $index);
        /*         * * get the end of the array ** */
        $end = array_slice($array, $index);
        /*         * * add the new element to the array ** */
        $start[] = $new_element;
        /*         * * glue them back together and return ** */
        return array_merge($start, $end);
    }

    public static function EncodeJavascript($text, $escapeChar = '"') {
        if ($escapeChar == '"')
            return str_replace($escapeChar, '\\"', $text);
        elseif ($escapeChar == '"')
            return str_replace($escapeChar, "\\'", $text);
        return $text;
    }

    /**
     * Redireziona il browser all'indirizzo specificato
     *
     * @param string $url Indirizzo verso cui redirezionare
     */
    public static function RedirectTo($url) {
        //ob_clean();
        //header("Location: " . $url);
        echo("<script>location.href = '".$url."';</script>");
        exit();
    }

    /**
     * Restituisce un array associativo da un oggetto recuperandone le proprieta'
     *
     * @param mixed $obj Oggetto da cui ricavare l'array
     * @return array Array associativo con le proprieta' e relativi valori
     *
     */
    public static function ObjectToArray($obj) {
        $array = array();
        $props = get_class_vars(get_class($obj));
        foreach ($props as $prop => $value) {
            $array[$prop] = $obj->$prop;
        }
        return $array;
    }

    public static function CreateRecursiveTree(&$tree, $a) {
        foreach ($a as $k => $v) {
            if (!array_key_exists($k, $tree)) {
                $tree[$k] = $v;
            } else {
                if (is_array($v)) {
                    Utils::CreateRecursiveTree($tree[$k], $v);
                }
            }
        }
    }

    public static function array_values_recursive($array) {
        $temp = array();
        foreach ($array as $key => $value) {

            if (is_numeric($key)) {
                $temp[] = is_array($value) ? Utils::array_values_recursive($value) : $value;
            } else {
                $temp[$key] = is_array($value) ? Utils::array_values_recursive($value) : $value;
            }
        }
        return $temp;
    }

    /*     * *****************************************************
     * DB UTILS 
     * ***************************************************** */

    public static function pdo_debugStrParams($stmt) {
        ob_start();
        $stmt->debugDumpParams();
        $r = ob_get_contents();
        ob_end_clean();
        return $r;
    }

    /**
     * Passando le variabili della classe viene costruita la sintassi della stringa sql (INSERT O UPDATE)
     * @param type $table
     * @param type $vars
     * @param type $varKeyAutoincrement nome colonna chiave autoincrement
     * @return string
     */
    public static function prepareQuery($table, $vars, $varKeyAutoincrement = 'ID') {
        $sottrai = (array_key_exists($varKeyAutoincrement, $vars) ? 1 : 0);
        if ($vars[$varKeyAutoincrement] > 0) {
            $sql = 'UPDATE  ' . $table . '  SET ';
            $i = 0;
            foreach ($vars as $k => $v) {
                if ($k == $varKeyAutoincrement)
                    continue;
                $i++;
                $sql .= $k . ' =:' . $k;
                if ($i < count($vars) - $sottrai)
                    $sql .= " , ";
            }
            $sql .= ' WHERE ' . $varKeyAutoincrement . ' = :' . $varKeyAutoincrement;
        } else {
            $sql = 'INSERT INTO ' . $table;
            $i = 0;
            foreach ($vars as $k => $v) {
                if ($k == $varKeyAutoincrement)
                    continue;
                $i++;
                if ($i == 1)
                    $sql .= " ( ";
                $sql .= $k;
                if ($i < count($vars) - $sottrai)
                    $sql .= " , ";
            }
            $sql .= " ) VALUES ( ";
            $ii = 0;
            foreach ($vars as $k => $v) {
                if ($k == $varKeyAutoincrement)
                    continue;
                $ii++;
                $sql .= ":" . $k . " ";
                if ($ii < count($vars) - $sottrai)
                    $sql .= " , ";
            }
            $sql .= " ) ";
            $sql .= ($get_return ? ' RETURNING ' . $varKeyAutoincrement : '');
        }
        //$sql = Utils::prepareSQLower($sql);
        return $sql;
    }

    public static function recJsonResponse($count, $row, $page, $record) {
        $result = array();
        $result[] = array(
            'totrec' => $count,
            'recs' => $record,
            'page' => $page,
            'row' => $row
        );
        return json_encode($result);
    }

    public static function saveJsonResponse($response) {
        $result = array();

        $result[] = array(
            'status' => $count,
            'recs' => $record,
            'page' => $page,
            'row' => $row
        );
        return json_encode($result);
    }

    /**
     * 
     * @param type $text
     * @param type $exit default false 
     */
    public static function print_debug($text = "", $exit = false) {
        echo "<pre><code style='background-color:yallow; color:#000000;'>DEBUG:<br/>" . $text . "</code></pre>";
        if ($exit) {
            exit();
        }
    }

    public static function print_array($array = array()) {
        echo "<PRE>" . print_r($array, true) . "</PRE>";
    }

    public static function print_xml($xml = '') {
        echo "<PRE>" . htmlentities($xml) . "</PRE>";
    }

    public static function normalizeGps($gps) {
        if (strpos($gps, 'N') !== false) {
            $gps = str_ireplace("' ", "", trim($gps));
            $gps = str_ireplace(". ", "", trim($gps));
            $gps = str_ireplace(".", "", trim($gps));
            $gps = str_ireplace("\"", "", trim($gps));
            $gps = str_ireplace("° ", ".", trim($gps));
            $gps = str_ireplace("E", "", trim($gps));
            $coord = explode("N", $gps);
        } else {
            $coord = explode(", ", $gps);
        }

        $lat[0] = trim($coord[0]);
        $lat[1] = trim($coord[1]);

        return $lat;
    }

    /*
     * Controllo se L'utente ha preso visione dell'accettazione dei dati 
     */

    public static function checkViewConsent() {
        global $LoggedAccount;

//        if ($_SERVER['REQUEST_URI'] != PERCORSO_CONSENSO) {
//            if ($LoggedAccount->CONSENSO == 0) {
//                header("Location:" . PERCORSO_CONSENSO);
//            }
//        }
    }

    /**
     * Restituisce il contenuto di un tag
     * @param type $string
     * @param type $tag_open
     * @param type $tag_close
     * @return type
     */
    public static function tag_contents($string, $tag_open, $tag_close) {
        foreach (explode($tag_open, $string) as $key => $value) {
            if (strpos($value, $tag_close) !== FALSE) {
                $result[] = substr($value, 0, strpos($value, $tag_close));
            }
        }
        return $result;
    }

    /**
     * Funzione per lo spelling dei numeri
     * @param type $num
     * @param type $centOOttanta
     * @return string
     */
    public static function spell_my_int($num, $centOOttanta = false) {
        $num = (int) $num;
        $mono = array("", "uno", "due", "tre", "quattro", "cinque", "sei", "sette", "otto", "nove");
        $duplo = array("dieci", "undici", "dodici", "{$mono[3]}dici", "quattordici", "quindici", "sedici", "dicias{$mono[7]}", "dici{$mono[8]}", "dician{$mono[9]}");
        $deca = array("", $duplo[0], "venti", "{$mono[3]}nta", "quaranta", "cinquanta", "sessanta", "settanta", "ottanta", "novanta");
        $cento = array("cent", "cento");
        $mili = array(
            0 => array("", "mille", "milione", "miliardo", "bilione", "biliardo"),
            1 => array("", "mila", "milioni", "miliardi", "bilioni", "biliardi")
        );
        $max = pow(10, count($mili[0]) * 3) - 1;
        if (!is_numeric($num)) {
            return "Non &egrave; un numero!";
        } elseif ($num < 0) {
            return "Numero negativo!";
        } elseif ($num > $max) {
            return "Limite superato!";
        } elseif ($num == 0) {
            return "zero";
        }
        $result = "";
        $sezione = 0;
        $num = (string) $num;
        switch (strlen($num) % 3) {
            case 1: $num = "00$num";
                break;
            case 2: $num = "0$num";
        }
        $numlen = strlen($num);
        while (($sezione + 1) * 3 <= $numlen) {
            $cifra = substr($num, (($numlen - 1) - (($sezione + 1) * 3)) + 1, 3);
            $numero = (int) $cifra;
            $cifra[0] = (int) $cifra[0];
            $cifra[1] = (int) $cifra[1];
            $cifra[2] = (int) $cifra[2];
            if ($numero <> 0) {
                $prime2cifre = (int) ($cifra[1] . $cifra[2]);
                if ($prime2cifre < 10) {
                    $text[2] = $mono[$cifra[2]];
                    $text[1] = "";
                } elseif ($prime2cifre < 20) {
                    $text[2] = "";
                    $text[1] = $duplo[$prime2cifre - 10];
                } else {
//	ventitre => ventitrè
                    if ($sezione == 0 && $cifra[2] == 3) {
                        $text[2] = "tr&egrave;";
                    } else {
                        $text[2] = $mono[$cifra[2]];
                    }
//	novantaotto => novantotto
                    if ($cifra[2] == 1 || $cifra[2] == 8) {
                        $text[1] = substr($deca[$cifra[1]], 0, -1);
                    } else {
                        $text[1] = $deca[$cifra[1]];
                    }
                }
                if ($cifra[0] == 0) {
                    $text[0] = "";
                } else {
//	centoottanta => centottanta
                    if (!$centOOttanta && $cifra[1] == 8 || ($cifra[1] == 0 && $cifra[2] == 8)) {
                        $IDcent = 0;
                    } else {
                        $IDcent = 1;
                    }
                    if ($cifra[0] <> 1) {
                        $text[0] = $mono[$cifra[0]] . $cento[$IDcent];
                    } else {
                        $text[0] = $cento[$IDcent];
                    }
                }
//	unomille	=> mille
//	miliardo	=> unmiliardo
                if ($numero == 1 && $sezione <> 0) {
                    if ($sezione >= 2) {
                        $result = "un" . $mili[0][$sezione] . $result;
                    } else {
                        $result = $mili[0][$sezione] . $result;
                    }
                } else {
                    $result = $text[0] . $text[1] . $text[2] . $mili[1][$sezione] . $result;
                }
            }
            $sezione++;
        }
        return $result;
    }

    public static function getClientIp() {
        $ip = getenv('HTTP_CLIENT_IP') ?: getenv('HTTP_X_FORWARDED_FOR') ?: getenv('HTTP_X_FORWARDED') ?: getenv('HTTP_FORWARDED_FOR') ?: getenv('HTTP_FORWARDED') ?: getenv('REMOTE_ADDR');
        return $ip;
    }

    public static function getJwt($fields = array(), $secretkey = NULL) {
        $header = array(
            'alg' => 'HS256',
            'typ' => 'JWT'
        );
        // Returns the JSON representation of the header
        $header = json_encode($header);
        //encodes the $header with base64.  
        $header = self::base64url_encode($header);
        $payload = json_encode($fields);
        $payload = self::base64url_encode($payload);
        $signature = hash_hmac('SHA256', "$header.$payload", $secretkey, true);
        $signature = self::base64url_encode($signature);
        $jwtcreated = "$header.$payload.$signature";
        return $jwtcreated;
    }

    public static function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function getUTF8($element = '') {
        $response = $element;
        if (!is_array($element)) {
            $response = utf8_encode($element);
        }
        return $response;
    }

    /**
     * 
     * @return string
     */
    public function randomHex() {
        $chars = 'ABCDEF0123456789';
        $color = '#';
        for ($i = 0; $i < 6; $i++) {
            $color .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $color;
    }
    /**
     * Restituisce il rgb e hex
     */
    public function randomColor(){
        $result = array('rgb' => '', 'hex' => '');
        $rgb = array('r', 'b', 'g');
        $result['rgb']= array();
        $r = '';
        foreach($rgb as $col){
            $rand = mt_rand(0, 255);
            $r.=$rand.",";
            $dechex = dechex($rand);
            if(strlen($dechex) < 2){
                $dechex = '0' . $dechex;
            }
            $result['hex'] .= $dechex;
        }
        $result['rgb'] = $r.' 1';
        return $result;
    }

    /*
     * Controllo la luminosita del colore
     */

    public static function getContrast50($hexcolor) {
        return (hexdec($hexcolor) > 0xffffff / 2) ? '000000' : 'ffffff'; //'black':'white';
    }

    /**
     * Restituisce il codice ATECO al netto dei punti e degli zeri finali
     * @param type $ateco
     * @return type
     */
    public static function normalizzaATECO($ateco = '') {
        $char = "0";
        $res = "";
        if ($ateco != "") {
            //echo "Originale ===> ".$ateco."<br>";
            $ateco = str_ireplace('.', '', $ateco);
            //echo "Senza punti ===> ".$ateco2."<br>";
            $ateco = rtrim($ateco, $char);
            //echo "Senza zeri finali ===> ".$ateco3."<br>";
            $res = $ateco;
        }
        return $res;
    }

//    public static function controllaPeriodoPreparazione() {
//        $oggi = new DateTime();
//        $dt_prep_Start = new DateTime(DATA_PREPARAZIONE_INIZIO);
//        $dt_prep_End = new DateTime(DATA_PREPARAZIONE_FINE);
//        $start_prep = Date::DateDiff('s', $oggi->format('Y-m-d H:i:s'), $dt_prep_Start->format('Y-m-d H:i:s'), false, false);
//        $end_prep = Date::DateDiff('s', $dt_prep_End->format('Y-m-d H:i:s'), $oggi->format('Y-m-d H:i:s'), false, false);
//        $preparazione = 0;
//        if ($start_prep < 0 && $end_prep < 0) {
//            $preparazione = 1;
//        } else {
//            $preparazione = 0;
//        }
//        return $preparazione;
//    }
//    public static function controllaPeriodoClickDay() {
//        $oggi = new DateTime();
//        $dt_pres_Start = new DateTime(DATA_PRESENTAZIONE_INIZIO);
//        $dt_pres_End = new DateTime(DATA_PRESENTAZIONE_FINE);
//        $start_pres = Date::DateDiff('s', $oggi->format('Y-m-d H:i:s'), $dt_pres_Start->format('Y-m-d H:i:s'), false, false);
//        $end_pres = Date::DateDiff('s', $dt_pres_End->format('Y-m-d H:i:s'), $oggi->format('Y-m-d H:i:s'), false, false);
//        $presentazione = 0;
//        if ($start_pres < 0 && $end_pres < 0) {
//            $presentazione = 1;
//        } else {
//            $presentazione = 0;
//        }
//        return $presentazione;
//    }

    public static function controllaPeriodoErogazione() {
        return false;
    }

    /**
     * Periodo apertura FAQ
     *  $showWrite => 0 Visualizzazione; 1: new Faq
     */
    public static function periodoFaq($showWrite = 0) {
        $oggi = new DateTime();
        $periodoFaq = 0;
        $dtStart = new DateTime(DATA_INIZIO_FAQ_UTENTI);
        $dtEnd = new DateTime(DATA_FINE_FAQ_UTENTI);
        if ($showWrite == 1) {
            $dtStart = new DateTime(DATA_INIZIO_NEW_FAQ_UTENTI);
            $dtEnd = new DateTime(DATA_FINE_NEW_FAQ_UTENTI);
        }
        $start = Date::DateDiff('s', $oggi->format('Y-m-d H:i:s'), $dtStart->format('Y-m-d H:i:s'), false, false);
        $end = Date::DateDiff('s', $dtEnd->format('Y-m-d H:i:s'), $oggi->format('Y-m-d H:i:s'), false, false);
        if ($start < 0 && $end < 0) {
            $periodoFaq = 1;
        } else {
            $periodoFaq = 0;
        }
        return $periodoFaq;
    }

    public static function loginAvailable() {
        global $statoSportello;
        $response = false;
        //if ($statoSportello == SPORTELLO_MANIFESTAZIONE || $statoSportello == SPORTELLO_PRESENTAZIONE || $statoSportello == SPORTELLO_RICHIESTA) {
        if ($statoSportello == SPORTELLO_PRESENTAZIONE) {
            $response = true;
        }
        return $response;
    }

    /* MANUTENZIONE => 1 ; PREPARAZIONE => 2 ; PREPARAZIONE_PRE => 3 ; PRESENTAZIONE_PRE => 4 ; PRESENTAZIONE => 5 ; PRESENTAZIONE_POST => 6 */

    public static function getStatoSportello($debug = false) {
        global $LEGENDA_STATI_SPORTELLO;
        $oggi = new DateTime();
        $response = 0;
        $manutenzione = 0;
        $manifestazione = 0;
        $presentazione = 0;
        $richiesta = 0;

        /* MANUTENZIONE */
        $dtStart = new DateTime(DATA_MANUTENZIONE_INIZIO);
        $dtEnd = new DateTime(DATA_MANUTENZIONE_FINE);
        $start = Date::DateDiff('s', $oggi->format('Y-m-d H:i:s'), $dtStart->format('Y-m-d H:i:s'), false, false);
        $end = Date::DateDiff('s', $dtEnd->format('Y-m-d H:i:s'), $oggi->format('Y-m-d H:i:s'), false, false);
        if ($start < 0 && $end < 0) {
            $manutenzione = 1;
        } else {
            $manutenzione = 0;
        }
        /* END MANUTENZIONE */

        /* PREPARAZIONE  */
//        $dt_prep_Start = new DateTime(DATA_MANIFESTAZIONE_INIZIO);
//        $dt_prep_End = new DateTime(DATA_MANIFESTAZIONE_FINE);
//        $start_prep = Date::DateDiff('s', $oggi->format('Y-m-d H:i:s'), $dt_prep_Start->format('Y-m-d H:i:s'), false, false);
//        $end_prep = Date::DateDiff('s', $dt_prep_End->format('Y-m-d H:i:s'), $oggi->format('Y-m-d H:i:s'), false, false);
//        $manifestazione = 0;
//        if ($start_prep < 0 && $end_prep < 0) {
//            $manifestazione = 1;
//        } else {
//            $manifestazione = 0;
//        }
        /* END PREPARAZIONE */

        /* PRESENTAZIONE */
        $dt_pres_Start = new DateTime(DATA_PRESENTAZIONE_INIZIO);
        $dt_pres_End = new DateTime(DATA_PRESENTAZIONE_FINE);
        $start_pres = Date::DateDiff('s', $oggi->format('Y-m-d H:i:s'), $dt_pres_Start->format('Y-m-d H:i:s'), false, false);
        $end_pres = Date::DateDiff('s', $dt_pres_End->format('Y-m-d H:i:s'), $oggi->format('Y-m-d H:i:s'), false, false);
        $presentazione = 0;
        if ($start_pres < 0 && $end_pres < 0) {
            $presentazione = 1;
        } else {
            $presentazione = 0;
        }

        /* RICHIESTA */
//        $dt_pres_Start = new DateTime(DATA_RICHIESTA_INIZIO);
//        $dt_pres_End = new DateTime(DATA_RICHIESTA_FINE);
//        $start_ric = Date::DateDiff('s', $oggi->format('Y-m-d H:i:s'), $dt_pres_Start->format('Y-m-d H:i:s'), false, false);
//        $end_ric = Date::DateDiff('s', $dt_pres_End->format('Y-m-d H:i:s'), $oggi->format('Y-m-d H:i:s'), false, false);
//        $richiesta = 0;
//        if ($start_ric < 0 && $end_ric < 0) {
//            $richiesta = 1;
//        } else {
//            $richiesta = 0;
//        }

        /* END PRESENTAZIONE */



        if (intval($manutenzione) == 1) {//SPORTELLO_MANUTENZIONE
            $response = SPORTELLO_MANUTENZIONE;
        } else if (intval($presentazione) == 1) {
            $response = SPORTELLO_PRESENTAZIONE;
        } else if (intval($presentazione) == 0 && $end_pres < 0) {
            $response = SPORTELLO_PRESENTAZIONE_PRE;
        } else if (intval($presentazione) == 0 && $end_pres > 0) {
            $response = SPORTELLO_PRESENTAZIONE_POST;
        }/* else if (intval($manifestazione) == 0 && intval($presentazione) == 0 && $end_pres > 0) {
          $response = SPORTELLO_PRESENTAZIONE_POST;
          }  else if (intval($richiesta) == 1) {
          $response = SPORTELLO_RICHIESTA;
          } else if (intval($manifestazione) == 0 && intval($presentazione) == 0 && intval($richiesta) == 0 && $end_ric < 0) {
          $response = SPORTELLO_PRESENTAZIONE_POST;
          } else if (intval($manifestazione) == 0 && intval($presentazione) == 0 && intval($richiesta) == 0 && $end_ric > 0) {
          $response = SPORTELLO_RICHIESTA_POST;
          } */
        if ($debug) {
            $response = $LEGENDA_STATI_SPORTELLO[$response];
        }

        return $response;
    }

    /**
     * Controllo il periodo di manutenzione
     * @return type boolean
     */
    public static function isStatoSportelloManutezione() {
        return self::getStatoSportello() == SPORTELLO_MANUTENZIONE;
    }

    /**
     * Controllo il periodo di manifestazione
     * @return type boolean
     */
//    public static function isStatoSportelloManifestazione($orMore = false) {
//        if ($orMore) {
//            return self::getStatoSportello() >= SPORTELLO_MANIFESTAZIONE;
//        } else {
//            return self::getStatoSportello() == SPORTELLO_MANIFESTAZIONE;
//        }
//    }

    /**
     * Controllo il periodo di presentazione
     * params $orMore (if true set control >= else == )
     * @return type boolean
     */
    public static function isStatoSportelloPresentazione($orMore = false) {
        if ($orMore) {
            return self::getStatoSportello() >= SPORTELLO_PRESENTAZIONE;
        } else {
            return self::getStatoSportello() == SPORTELLO_PRESENTAZIONE;
        }
    }

    /**
     * Controllo il periodo di richiesta
     * params $orMore (if true set control >= else == )
     * @return type boolean
     */
//    public static function isStatoSportelloRichiesta($orMore = false) {
//        if ($orMore) {
//            return self::getStatoSportello() >= SPORTELLO_RICHIESTA;
//        } else {
//            return self::getStatoSportello() == SPORTELLO_RICHIESTA;
//        }
//    }

    /**
     * Controllo il periodo di manutenzione
     * @return type boolean
     */
    public static function isStatoSportelloAmmissionePresentazione($orMore = false) {
        if ($orMore) {
            return self::getStatoSportello() >= SPORTELLO_PRESENTAZIONE_PRE;
        } else {
            return self::getStatoSportello() == SPORTELLO_PRESENTAZIONE_PRE;
        }
    }

    /**
     * Controllo il periodo di manutenzione
     * @return type boolean
     */
//    public static function isStatoSportelloAmmissioneContributo($orMore = false) {
//        if ($orMore) {
//            return self::getStatoSportello() >= SPORTELLO_PRESENTAZIONE_POST;
//        } else {
//            return self::getStatoSportello() == SPORTELLO_PRESENTAZIONE_POST;
//        }
//    }

    public static function checkAuthStatoSportelloHome() {
        $statoSportello = self::checkAuthStatoSportello();
        if (isset($_SESSION['ID']) && intval($_SESSION['ID']) > 0 && (stripos($_SERVER['PHP_SELF'], "index.php") !== FALSE || stripos($_SERVER['PHP_SELF'], "loginspid.php") !== FALSE || stripos($_SERVER['PHP_SELF'], "login_spid.php") !== FALSE)) {
            //if ($statoSportello == SPORTELLO_MANIFESTAZIONE || $statoSportello == SPORTELLO_PRESENTAZIONE || $statoSportello == SPORTELLO_RICHIESTA) {
            if ($statoSportello == SPORTELLO_PRESENTAZIONE || $statoSportello == SPORTELLO_PRESENTAZIONE_POST) {
                Utils::RedirectTo(HTTP_PRIVATE_SECTION . 'dashboard.php');
                exit;
            }
        }
        return $statoSportello;
    }

    public static function getCurrentPageCalled() {
        return strtolower(basename($_SERVER['SCRIPT_FILENAME']));
    }

    public static function isPrivatePageCalled() {
        return strpos($_SERVER['SCRIPT_FILENAME'], PRIVATE_SECTION) !== FALSE ? true : false;
    }

    public static function checkAuthStatoSportello() {
        global $pageAccessNoControll;
        $statoSportello = self::getStatoSportello();
        $currentPage = self::getCurrentPageCalled();
        $availableAccess = (in_array($currentPage, $pageAccessNoControll) || (Utils::checkCurrentPage('_webservice') && isset($_REQUEST["module"]) && $_REQUEST["module"] == "faq" && isset($_REQUEST["action"]) && $_REQUEST["action"] == "listFaq"));
        //if ($statoSportello == SPORTELLO_MANUTENZIONE || $statoSportello == SPORTELLO_PRESENTAZIONE_PRE || $statoSportello == SPORTELLO_PRESENTAZIONE_POST) {
        //if (!$availableAccess && ($statoSportello == SPORTELLO_MANUTENZIONE || $statoSportello == SPORTELLO_PRESENTAZIONE_PRE || $statoSportello == SPORTELLO_PRESENTAZIONE_POST)) {
        if (!$availableAccess && ($statoSportello == SPORTELLO_MANUTENZIONE)) {
            Utils::RedirectTo(BASE_HTTP . 'info.php');
            exit;
        }

        return $statoSportello;
    }

    /* return field for doc from spid idCard field in response */

    public static function splitDocSPID($idCard) {
        $response = array('TIPO_DOCUMENTO' => '', 'NUMERO_DOCUMENTO' => '', 'ENTE_RILASCIO' => '', 'DATA_EMISSIONE' => '', 'DATA_SCADENZA' => '');
        $idCardArray = explode(" ", $idCard);
        $response['TIPO_DOCUMENTO'] = $idCardArray[0];
        $response['NUMERO_DOCUMENTO'] = $idCardArray[1];

        /* BUG FIX TIMSPID */
        if (Date::is_date($idCardArray[3])) {
            $response['ENTE_RILASCIO'] = $idCardArray[2];
            $response['DATA_EMISSIONE'] = date("d-m-Y", strtotime($idCardArray[3]));
            $response['DATA_SCADENZA'] = date("d-m-Y", strtotime($idCardArray[4]));
        } else {
            $response['ENTE_RILASCIO'] = $idCardArray[2] . " " . $idCardArray[3];
            $response['DATA_EMISSIONE'] = date("d-m-Y", strtotime($idCardArray[4]));
            $response['DATA_SCADENZA'] = date("d-m-Y", strtotime($idCardArray[5]));
        }
        return $response;
    }

    /**
     * Return code encrypt
     * @param type $codice
     * @return type
     * 
     */
    public static function decryptCode($codice = "") {
        $codice_encrypt = openssl_decrypt($codice, METODO_CRITTOGRAFIA, CHIAVE_CRITTOGRAFIA, 0, STRINGA_RANDOM);
        return $codice_encrypt;
    }

    /**
     * Get defult array response in class method
     */
    public static function initDefaultResponse($value = -999, $message = "Si è verificato un errore") {
        return array("esito" => $value, "erroreDescrizione" => $message);
    }

    /**
     * Get defult array response fot DataTable in class method
     */
    public static function initDefaultResponseDataTable($value = -999, $message = "Si è verificato un errore") {
        return array(
//            "draw" => 999,
            "iTotalRecords" => "0",
            "iTotalDisplayRecords" => "0",
            "aaData" => array(),
            "esito" => $value,
            "erroreDescrizione" => $message
        );
    }

    /**
     * Get defult array response in class method with records response
     */
    public static function initDefaultRowsResponse($value = -999, $rows = array(), $message = "Si è verificato un errore") {
        return array("esito" => $value, "rows" => $rows, "erroreDescrizione" => $message);
    }

//    public static function getResponseMsg(){
//        return 'Operazione non consentita in questa fase.';
//    }

    /*
     * Fn for check esito operation response (success OR failure)
     */

    public static function checkResponse($response = array()) {
        $return = isset($response['esito']) && $response['esito'] == 1 ? true : false;
    }

    /*
     * FN SET FIELD PROPERTIES
     */

    public static function getSingleFieldState($confirm = true, $state = 'readonly') {
        $response = "";
        if ($confirm) {
            switch ($state) {
                case 'readonly':
                    $response = "readonly='readonly'";
                    break;
                case 'hidden':
                    $response = "hidden='true'";
                    break;
                case 'readonly':
                    $response = "disabled='true'";
                    break;
            }
        }
        return $response;
    }

    public static function getFieldsState($confirm) {
        $response = array(
            'readonly' => "",
            'hidden' => "",
            'disabled' => "",
            'stato' => 1
        );
        if (!$confirm) {
            $response = array(
                'readonly' => "readonly='readonly'",
                'hidden' => "hidden='true'",
                'disabled' => "disabled='true'",
                'stato' => 0
            );
        }
        return $response;
    }

    /*
     *  Check if invalid emptyField only for fields not in array exclude
     */

    public static function validFieldSave($class = '', $inputs = array(), $exclude = array()) {
        global $LoggedAccount;
        $fields = get_object_vars(new $class());
        foreach ($fields as $key => $value) {
            if (!in_array($key, $exclude) && (!isset($inputs[$key]) || empty($inputs[$key]))) {
                return false;
            }
            /* CUSTOM CONTROLS */
//            switch ($class){
//                'AccountAnagrafica':
//                    if ($key == 'CODICE_FISCALE' && $inputs[$key] != $LoggedAccount->CODICE_FISCALE){
//                        return false;
//                    }
//                    break;
//            }
        }
        return true;
    }

    /*
     * Check se il CF dell'utente loggato è uguale a quello che sto salvando
     */

    public static function validSavedAccount($prop = 'ID', $value = '') {
        global $LoggedAccount;
        $response = false;
        if (!empty($prop) && !empty($value)) {
            $response = ($value == $LoggedAccount->$prop ? true : false);
        }
        return $response;
    }

    /**
     * Restituisce un numero di protocollo anteponendo l'anno e il mese in corso o della data passata alla funzione     
     * @param type $numero
     * @param type $data
     * @param type $char_pad
     * @param type $number_pad
     * @return type varchar  esempio di return 20201100000003
     */
    public static function getProtocollo($numero = '', $data = '', $char_pad = 0, $number_pad = 6) {

        $data_protocollo = ($data == '' ? date('Y-m-d') : $data);
        $anno = date('Ym', strtotime($data_protocollo));
        return $anno . str_pad($numero, $number_pad, $char_pad, STR_PAD_LEFT);
    }

    public static function checkCurrentPage($page) {
        if ($page != '') {
            $response = stripos($_SERVER['PHP_SELF'], $page . ".php") !== FALSE;
        }
        return $response;
    }

    /**
     * Verifica pagina corrente index o loginspid
     */
    public static function checkPreLoginPage() {
        $response = false;
        if (stripos($_SERVER['PHP_SELF'], "logout.php") !== FALSE) {
            $response = true;
        }
        return $response;
    }

    /**
     * restituisce l'indirizzo ip del chiamante: 
     * $checkRange => true considera solo gli IP PUBBLICI, false considera anche gli IP PRIVATI
     */
    public static function get_IP_address($checkRange = true) {
        $filterFlag = ($checkRange ? FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE : '');
        foreach (array('HTTP_CLIENT_IP',
    'HTTP_X_FORWARDED_FOR',
    'HTTP_X_FORWARDED',
    'HTTP_X_CLUSTER_CLIENT_IP',
    'HTTP_FORWARDED_FOR',
    'HTTP_FORWARDED',
    'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $IPaddress) {
                    $IPaddress = trim($IPaddress); // Just to be safe

                    if (filter_var($IPaddress, FILTER_VALIDATE_IP, $filterFlag) !== false) {
                        return $IPaddress;
                    }
                }
            }
        }
    }

    public static function access_IP_control() {
        global $LoggedAccount;
        return (!ENABLE_CHECK_IP || (!empty($LoggedAccount->IP) && $LoggedAccount->IP == Utils::get_IP_address()) ? true : false);
    }

    public static function getFaseDocumenti() {
        return (Utils::isStatoSportelloPresentazione() ? DOCUMENTI_FASE_2 : (Utils::isStatoSportelloRichiesta() ? DOCUMENTI_FASE_3 : DOCUMENTI_FASE_1));
    }

    public static function getServiceErrorText($msg) {
        $response = $msg;
        if (strpos(strtolower($msg), "unique constraint") >= 0) {
            $response = "Non è possibile inserire un record duplicato";
        }
        return $response;
    }

    /**
     * Prepare la query per l'utilizzo in PGSQL
     */
    public static function prepareSQLower($str, $needle = ':', $exc = false) {
        
        
        $matchesField = array_filter(str_word_count($str, 2, $needle . '_'),
                function($item) use ($needle) {
            return (levenshtein($item, $needle, 1, 1, 0) == 0);
        }
        );
        $sql_lower = strtolower($str);
        
        
        $needle2 = "";
        $matchesObjDB = array_filter(str_word_count($sql_lower, 2, $needle2 . '_'),
                function($item) use ($needle2) {
            return (levenshtein($item, $needle2, 1, 1, 0) == 0);
        }
        );
        $exclude = array('*', 'on', 'join' ,'inner', 'left', 'right', 'call', 'select', 'from', 'where', 'and', 'or', 'insert', 'to', 'update', 'set', 'delete', 'returning', 'into', 'values', 'order', 'by', 'limit', 'offset', 'between', 'like', 'in', 'is', 'isnull', 'notnull', 'not', 'distinct', 'group', 'having', 'asc', 'desc', 'as');
        $matchesObjDB = array_unique(array_values(array_diff($matchesObjDB, $exclude)));
        
        
        $sql_upper = "";
        foreach ($matchesField as $value) {
            $sql_upper = str_replace(strtolower($value), $value, ($sql_upper == "" ? $sql_lower : $sql_upper));
        }
        $sql_upper = ($sql_upper == "" ? $sql_lower :  $sql_upper);
        
        $sql_upper_reply = "";
        foreach ($matchesObjDB as $value) {
            $sql_upper_reply = str_replace(' ' . $value . ' ', ' "' . $value . '" ', ($sql_upper_reply == "" ? $sql_upper : $sql_upper_reply));
        }
        $sql_return = '';
        //echo $sql_upper_reply;
        //Utils::print_array($matchesObjDB);
        
        if ($exc) {
            $sql_return = ($sql_upper_reply == "" ? strtoupper($sql_upper) : strtoupper($sql_upper_reply));
        } else {
            $sql_return = $sql_lower;
        }
        return $sql_return;
    }
    
    /**
     * Converte il campo shape text in punti da poter utilizzare com SVG
     * @param type $shape_text
     * @param type $dividendo
     * @return string
     */
    public static function normalizePolygonToPoints($shape_text, $dividendo = 10000) {
        $polygon = explode('POLYGON (', $shape_text);
        $shapes = substr($polygon[1],0,-1);
        $shape = explode("), (", $shapes);
        $coord = array();
        foreach ($shape as $v) {
            $v = str_ireplace("(", "", trim($v));
            $v = str_ireplace(")", "", trim($v));
            $points = explode(',', trim($v));            
            $str = '';
            $allp = count($points);
            $cnts =0;
            foreach ($points as $p) {
                $cnts++;
                $point = explode(' ', trim($p));
                $diviso = array();
                $cnt =0;                
                foreach ($point as $value) {
                    $cnt++;
                    $str.= ((($value)/$dividendo)).' ';
                    if($cnt %2 == 0 && $cnts<$allp ){
                        $str.= ',';
                    }
                }
            }
            $coord[] = $str;
        }
        return $coord;
        
    }
    
    /**
     * Filtra un array passando la chiave e il valore
     * @param type $array
     * @param type $index
     * @param type $value
     * @return type
     */
    public static function filter_by_value ($array, $index, $value){
        if(is_array($array) && count($array)>0) 
        {
            foreach(array_keys($array) as $key){
                $temp[$key] = $array[$key][$index];
                
                if ($temp[$key] == $value){
                    $newarray[$key] = $array[$key];
                }
            }
          }
      return array_values($newarray);
    }
    
    /**
     * 
     * @param type $method // Method: POST, PUT, GET etc
     * @param type $url
     * @param type $data // Data: array("param" => "value") ==> index.php?param=value
     * @return type
     */
    public static function CallAPI($method, $url, $data = false)
    {
        $curl = curl_init();

        switch ($method)
        {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);

                if ($data)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_PUT, 1);
                break;
            default:
                if ($data)
                    $url = sprintf("%s?%s", $url, http_build_query($data));
        }
        
        
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));        
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        
        // Optional Authentication:
        //curl_setopt($curl, CURLOPT_HTTPAUTH, false);
        //curl_setopt($curl, CURLOPT_USERPWD, "username:password");


        $result = curl_exec($curl);

        curl_close($curl);

        return $result;
    }
    
    
    public static function getQIAM($token = ""){
        
        $endpoint = ENDPOINTQIAM ;
    
        
        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer ".$token,
         );
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);        
        
        
        
        //curl_setopt($ch, CURLOPT_POST, true);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', $authorization));
        $result = curl_exec($ch);
        $arrayresponse = json_decode($result, true);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_errno = curl_errno($ch);
        if ($http_status == 200) {
            $return['esito'] = 1;
            $return['token'] = $token;
            $return['dati'] = $arrayresponse;
        } else {        
            $return['esito'] = -999;
            $return['token'] = $token;
            $return['erroreDescrizione'] = "Servizio Momentaneamente Non Disponibile";

        }
        curl_close($ch);
        
        return($return);
    }

}
