<?php

class Date {

    public static function calcolaGiorni($scadenza, $data) {
        $startTimeStamp = strtotime($data);
        $endTimeStamp = strtotime($scadenza);
        $timeDiff = abs($endTimeStamp - $startTimeStamp);
        $numberDays = $timeDiff / 86400;  // 86400 seconds in one day
        // and you might want to convert to integer
        $numberDays = intval($numberDays);
        return $numberDays;
    }

    public static function time12to24($time = null) {
        $result = '';
        if ($time != null) {
            $ora_ampm = explode(' ', $time);
            $ora = explode(':', $ora_ampm[0]);
            if ($ora_ampm[1] == 'PM') {
                $ora[0] = $ora[0] + 12;
            }
            $result = $ora[0] . ':' . $ora[1];
        }
        return $result;
    }

    public static function is_date($date) {
        $date = str_replace(array('\'', '-', '.', ','), '/', $date);
        $date = explode('/', $date);

        if (count($date) == 1 // No tokens
                and is_numeric($date[0])
                and $date[0] < 20991231 and ( checkdate(substr($date[0], 4, 2)
                        , substr($date[0], 6, 2)
                        , substr($date[0], 0, 4)))
        ) {
            return true;
        }

        if (count($date) == 3
                and is_numeric($date[0])
                and is_numeric($date[1])
                and is_numeric($date[2]) and ( checkdate($date[0], $date[1], $date[2]) //mmddyyyy
                or checkdate($date[1], $date[0], $date[2]) //ddmmyyyy
                or checkdate($date[1], $date[2], $date[0])) //yyyymmdd
        ) {
            return true;
        }

        return false;
    }

    public static function convertData($date, $format = "d-m-Y H:i:s") {
        $newDate = date("d/m/Y", strtotime($date));
        return $newDate;
    }

    public static function convertiDataUSA($date) {
        list ($giorno, $mese, $anno) = split('[/.-]', $date);
        return $anno . "/" . $mese . "/" . $giorno;
    }

    public static function GetAge($dataNascita) {
        if ($dataNascita == "" || $dataNascita == '00/00/0000' || $dataNascita == '0000-00-00')
            return 0;
        // Ricavo giorno, mese e anno
        list($giorno, $mese, $anno) = explode("/", Utils::FormatDate($dataNascita, DATE_FORMAT_ITA));
        // Calcolo anni
        $eta = date('Y') - $anno;
        // Tolgo 1 se ad esempio sono X anni e 11 mesi
        if (date('m') < $mese)
            $eta--;
        // Stessa cosa per i giorni
        elseif (date('m') == $mese && date('d') < $giorno)
            $eta--;
        return $eta;
    }

    /**
     * 
     * @param type /**
      $interval can be:
      yyyy - Number of full years
      q - Number of full quarters
      m - Number of full months
      y - Difference between day numbers (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
      d - Number of full days
      w - Number of full weekdays
      ww - Number of full weeks
      h - Number of full hours
      n - Number of full minutes
      s - Number of full seconds (default)
     * @param type $datefrom
     * @param type $dateto
     * @param type $using_timestamps
     * @param type $return_absolute_diff
     * @param type $addDayTo aggiunge un giorno allla data fine periodo
     * @return type
     */
    public static function DateDiff($interval, $datefrom, $dateto, $using_timestamps = false, $return_absolute_diff = true, $addDayTo = false) {
        if (!$using_timestamps) {
            $datefrom = strtotime(str_replace("/", "-", $datefrom), 0);
            if ($addDayTo) {
                $dateto = strtotime(str_replace("/", "-", $dateto) . '+1 day', 0);
            } else {
                $dateto = strtotime(str_replace("/", "-", $dateto), 0);
            }
        }

        $difference = $dateto - $datefrom; // Difference in seconds

        switch ($interval) {
            case 'y': // Number of full years
                $years_difference = floor($difference / 31536000);
                if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom) + $years_difference) > $dateto) {
                    $years_difference--;
                }
                if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto) - ($years_difference + 1)) > $datefrom) {
                    $years_difference++;
                }
                $datediff = $years_difference;
                echo "<br>DIFF:".$datediff."<br>";
                break;
            case "q": // Number of full quarters
                $quarters_difference = floor($difference / 8035200);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($quarters_difference * 3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $quarters_difference++;
                }
                $quarters_difference--;
                $datediff = $quarters_difference;
                break;
            case "m": // Number of full months
                $months_difference = floor($difference / 2678400);
                while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom) + ($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
                    $months_difference++;
                }
                $months_difference--;
                $datediff = $months_difference;
                break;
            case 'z': // Difference between day numbers
                $datediff = date("z", $dateto) - date("z", $datefrom);

                break;
            case "d": // Number of full days
                $datediff = floor($difference / 86400);
                break;
            case "w": // Number of full weekdays
                $days_difference = floor($difference / 86400);
                $weeks_difference = floor($days_difference / 7); // Complete weeks
                $first_day = date("w", $datefrom);
                $days_remainder = floor($days_difference % 7);
                $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
                if ($odd_days > 7) {
// Sunday
                    $days_remainder--;
                }
                if ($odd_days > 6) {
// Saturday
                    $days_remainder--;
                }
                $datediff = ($weeks_difference * 5) + $days_remainder;
                break;
            case "ww": // Number of full weeks
                $datediff = floor($difference / 604800);
                break;
            case "h": // Number of full hours
                $datediff = floor($difference / 3600);
                break;
            case "n": // Number of full minutes
                $datediff = floor($difference / 60);
                break;
            default: // Number of full seconds (default)
                $datediff = $difference;
                break;
        }
        return $return_absolute_diff ? abs($datediff) : $datediff;
    }

    function GetTimestamp($data, $add_days = 0, $debug = false) {

        if (strpos($data, " ") === false) {
            $a_data = $data;
            $a_time = "00:00";
        } else {
            $a_data = substr($data, 0, strpos($data, " "));
            $a_time = substr($data, strpos($data, " ") + 1, 8);
        }

        $a_data = explode("/", str_replace("-", "/", $a_data));
        $a_time = explode(":", str_replace(".", ":", $a_time));
        if (strlen($a_data[0]) == 4) {
// Arriva in formato ISO
            $timestamp = mktime($a_time[0], $a_time[1], $a_time[2], $a_data[1], $add_days + $a_data[2], $a_data[0]);
        } else {
// Arriva in formato ITA
            $timestamp = mktime($a_time[0], $a_time[1], $a_time[2], $a_data[1], $add_days + $a_data[0], $a_data[2]);
        }

        return $timestamp;
    }

    /**
     * Data una stringa, ne restituisce la data corrispondente nel formato richiesto.
     *
     * @param string $data La stringa contenente la data in formato italiano
     * @param mixed $format Deve essere DATE_FORMAT_ITA oppure DATE_FORMAT_ISO
     * @return string
     */
    public static function FormatDate($data = null, $format = DATE_FORMAT_ITA) {
        if ($data == '')
//            return "";
            return null;
        if ($data == null)
            $data = date("d/m/Y");
        $len = strlen($data);
        if ($len != 10 && $len != 16 && $len != 19)
            return $data;
        if ($len == 10) {
            list($d1, $d2, $d3) = explode("/", str_replace("-", "/", $data));
            if (strlen($d1) == 4) {
                $year = $d1;
                $month = $d2;
                $day = $d3;
            } else {
                $day = $d1;
                $month = $d2;
                $year = $d3;
            }
//echo "F: ".$format; exit;
            switch ($format) {
                case DATE_FORMAT_ITA:
                    return sprintf("%s/%s/%s", $day, $month, $year);
                case DATE_FORMAT_ISO:
                    return sprintf("%s-%s-%s", $year, $month, $day);
                case DATE_FORMAT_ITA_WITHOUT_SEP:
                    return sprintf("%s%s%s", $day, $month, $year);
                default:
                    $timestamp = mktime(0, 0, 0, $month, $day, $year);
                    return strftime($format, $timestamp);
            }
        } else {
            $array = explode(" ", $data);
            $date = $array[0];
            $ora = $array[1];
            list($d1, $d2, $d3) = explode("/", str_replace("-", "/", $date));
            list($hour, $minute) = explode(":", str_replace(".", ":", $ora));
            if (strlen($d1) == 4) {
                $year = $d1;
                $month = $d2;
                $day = $d3;
            } else {
                $day = $d1;
                $month = $d2;
                $year = $d3;
            }
            switch ($format) {
                case DATE_FORMAT_ITA:
                    return sprintf("%s/%s/%s %s:%s:00", $day, $month, $year, $hour, $minute);
                case DATE_FORMAT_ISO:
                    return sprintf("%s-%s-%s %s:%s:00", $year, $month, $day, $hour, $minute);
                case DATE_FORMAT_ITA_WITHOUT_SEP:
                    return sprintf("%s%s%s", $day, $month, $year, $hour, $minute);
                default:
                    $timestamp = mktime(0, 0, 0, $month, $day, $year);
                    return strftime($format, $timestamp);
            }
        }
    }

    public static function GetInizioSettimana($Data) {
        $myDate = strtotime($Data);
        $giornoSettimana = date("w", $myDate);
        $differenzaGiorni = $giornoSettimana - 1;
        $giornoCheFu = date("Y-m-d", strtotime($Data . "-" . $differenzaGiorni . " days"));
        return $giornoCheFu;
    }

    public static function GetFineSettimana($Data) {
//$myDate = strtotime($Data);
        $giornoSettimana = date("w", $Data);
        $giornoCheSara = date("Y-m-d", strtotime($Data . "+" . $giornoSettimana . " days"));
        return $giornoCheSara;
    }

    public static function AddTime($Start, $Adding, $unit, $diff = "+") {

        $Start = strtotime($Start);
        switch ($unit) {
            case "hours":
                $etime = strtotime("$diff $Adding hours", $Start);
                return date('H:i:s', $etime);
            case "minutes":
                $etime = strtotime("$diff $Adding minutes", $Start);
                return date('H:i:s', $etime);
            case "seconds":
                $etime = strtotime("$diff $Adding seconds", $Start);
                return date('H:i:s', $etime);
        }
    }

    public static function GetGiornoSettimana($data, $all = false) {
        if ($data == "")
            return "";
        $giorni = array('Domenica', 'Luned&igrave;', 'Marted&igrave;', 'Mercoled&igrave;',
            'Gioved&igrave;', 'Venerd&igrave;', 'Sabato');
        $data = Utils::FormatDate($data, DATE_FORMAT_ISO);
        if ($all)
            return $giorni[date('w', strtotime($data))];
        else
            return substr($giorni[date('w', strtotime($data))], 0, 3);
    }

    public static function getFormattedDataNascita($data_nascita) {
        if (!$data_nascita)
            return '';
        $m = substr($data_nascita, 3, 3);
        $d = date("d", strtotime($data_nascita));
        $months = array("Jan" => "01", "Feb" => "02", "Mar" => "03", "Apr" => "04", "May" => "05", "Jun" => "06", "Jul" => "07", "Aug" => "08", "Sept" => "09", "Oct" => "10", "Nov" => "11", "Dec" => "12");
        if (array_key_exists($m, $months)) {
            $m = $months[$m];
        } else {
            $m = date("m", strtotime($data_nascita));
        }
        $y = date("Y", strtotime($data_nascita));
        if ($y > date('Y')) {
            $y = $y - 100;
        }
        return $d . "-" . $m . "-" . $y;
    }

    public static function dateOracleTimeStamp($data = "", $format = FORMAT_TIMESTAMP_ORACLE/* 'd-M-Y H:i:s.u' */) {
        if (!$data)
            return '';
        $converteddate = DateTime::createFromFormat("d-M-y h.i.s.u A", $data);
        $DateTime = $converteddate->format($format);
        return $DateTime;
    }

    public static function dateOracleDateNascita($data = "", $format = 'd-m-Y') {
        if (!$data)
            return '';
//        $m = substr($data, 3, 3);
//        $months = array("Jan" => "01", "Feb" => "02", "Mar" => "03", "Apr" => "04", "May" => "05", "Jun" => "06", "Jul" => "07", "Aug" => "08", "Sept" => "09", "Oct" => "10", "Nov" => "11", "Dec" => "12");
//        if (array_key_exists($m, $months)){
//            $months[$m];
//        }
        $converteddate = new DateTime($data);
        $y = $converteddate->format("Y");
        if ($y > date('Y')) {
            $y = $y - 100;
        }
        $DateTime = $converteddate->format("m-d");
        $DateTime = $y . "-" . $DateTime;
        $DateTime = date($format, strtotime($DateTime));
        return $DateTime;
    }

    public static function dateOracleDate($data = "", $format = 'd-m-Y') {
        if (!$data)
            return '';
        $converteddate = new DateTime($data);
        $DateTime = $converteddate->format($format);
        return $DateTime;
    }

    /**
     * Controllo Date
     * Controllare anche che la data non sia superiore alla data di inserimento della pratica (Oggi)
     * @param $dataInizio permette di controllare una data iniziale con la data di nascita dell'utente loggato 
     * @param $dataFine permette di controllare una data finale con la data di nascita dell'utente loggato 
     * @param $controlloDataAttuale tramite specifica booleane si può decidere se verifare o meno la data inserita con la data attuaale
     * @param $controlloDataConstant tramite specifica boolean si può decidere se verifica o meno con la data imposta nelle constant
     */
    public static function minorDate($dataInizio = "", $dataFine = "", $controlloDataAttuale = false, $controlloDataConstant = false) {
        global $LoggedAccount;
        $booleanVerify = false;
        if ($dataInizio != "") {
            $dataInizio = new DateTime($dataInizio);
            $dataFine = new DateTime($dataFine);
            $dataNascita = date("Y-m-d", strtotime($LoggedAccount->Anagrafica->DATA_NASCITA));
            $dataDueNascita = new DateTime($dataNascita);
            $booleanVerify = ($dataInizio->format("Y-m-d") < $dataDueNascita->format("Y-m-d") ? false : true);
            if ($booleanVerify) {
                if ($controlloDataAttuale) {
                    if (!Date::verifyMinorDate($dataInizio, new DateTime(date("Y-m-d"))) || !Date::verifyMinorDate($dataFine, new DateTime(date("Y-m-d")))) {
                        return false;
                    }
                }
                if ($controlloDataConstant) {
                    if (!Date::verifyMinorDate($dataInizio, new DateTime(DATA_FINE_CALCOLO_ESPERIENZA)) || !Date::verifyMinorDate($dataFine, new DateTime(DATA_FINE_CALCOLO_ESPERIENZA))) {
                        return false;
                    }
                }
            }
        }
        return $booleanVerify;
    }

//
    public static function verifyMinorDate($dataRicevuta = "", $dataDiControllo = "") {
        return ($dataRicevuta->format("Y-m-d") > $dataDiControllo->format("Y-m-d") ? false : true);
    }

    public static function rangeDate($dataInizio = "", $dataFine = "") {
        global $LoggedAccount;
        $return = array();
        $dataNascita = new DateTime($LoggedAccount->Anagrafica->DATA_NASCITA);
        $dataAttuale = new DateTime(DATA_CONTROLLO_ATTUALE);
        if ($dataFine != "") {
            $dataInizio = new DateTime($dataInizio);
            $dataFine = new DateTime($dataFine);
            if ($dataInizio->getTimestamp() > $dataNascita->getTimestamp() && $dataFine->getTimestamp() <= $dataAttuale->getTimestamp()) {
                $return['esito'] = 1;
            } else {
                $return['erroreDescrizione'] = "<b>Attenzione il periodo di riferimento inserimento non è corretto!</b>";
            }
        } else {
            $dataInizio = new DateTime($dataInizio);
            if ($dataInizio->getTimestamp() > $dataNascita->getTimestamp() && $dataInizio->getTimestamp() <= $dataAttuale->getTimestamp()) {
                $return['esito'] = 1;
            } else {
                $return['erroreDescrizione'] = "<b>Attenzione, data non corretta!</b>";
            }
        }

        return $return;
    }

    /**
     * Controllo se la data utilizzata corrisponde ai parametri richiesti. 
     * @global type $LoggedAccount
     * @param type $dataInizio
     * @param type $dataFine
     * @param type $checkBirthday
     * @param type $checkToDay
     * @return string
     */
    public static function checkSaveDate($dataInizio = "", $dataFine = "", $checkBirthday = CONTROLLO_BIRTHDATE, $checkToDay = CONTROLLO_TODATE) {
        global $LoggedAccount;
        $response = Utils::initDefaultResponse();
        if (empty($dataInizio)) {
            $response['erroreDescrizione'] = "<b>Inseriere un data valida!</b>";
        } else if (!empty($dataInizio) && empty($dataFine)) {
            if (!Date::is_date($dataInizio)) {
                $response['erroreDescrizione'] = "Attenzione, la <b>data</b> inserita non è una data valida";
            } else {
                $birthday = ($checkBirthday ? new DateTime($LoggedAccount->Anagrafica->DATA_NASCITA) : new DateTime($dataInizio));
                $today = ($checkToDay ? (DATA_CONTROLLO_ATTUALE != "" ? new DateTime(DATA_CONTROLLO_ATTUALE) : new DateTime()) : new DateTime($dataInizio) );
                $from = new DateTime($dataInizio);
                if ($from < $today && $from > $birthday) {
                    $response['esito'] = 1;
                } else {
                    if ($from >= $today) {
                        $response['erroreDescrizione'] = "<b>Attenzione la data inserita (" . $dataInizio . ") deve essere precedente alla data odienra!</b>";
                    } elseif ($from <= $birthday) {
                        $response['erroreDescrizione'] = "<b>Attenzione la data inserita (" . $dataInizio . ") deve essere successiva alla tua data di nascita!</b>";
                    } else {
                        $response['erroreDescrizione'] = "Attenzione, errore nella data!";
                    }
                }
            }
        } else {
            if (!Date::is_date($dataInizio)) {
                $response['erroreDescrizione'] = "La <b>data inizio</b> non è una data valida";
                return $response;
            }
            if (!Date::is_date($dataFine)) {
                $response['erroreDescrizione'] = "La <b>data fine</b> non è una data valida";
                return $response;
            }

            $from = new DateTime($dataInizio);
            $to = new DateTime($dataFine);
            if ($from <= $to) {
                $birthdayF = ($checkBirthday ? new DateTime($LoggedAccount->Anagrafica->DATA_NASCITA) : new DateTime($dataInizio));
                $birthdayT = ($checkBirthday ? new DateTime($LoggedAccount->Anagrafica->DATA_NASCITA) : new DateTime($dataFine));
                $todayF = ($checkToDay ? (DATA_CONTROLLO_ATTUALE != "" ? new DateTime(DATA_CONTROLLO_ATTUALE) : new DateTime()) : new DateTime($dataInizio) );
                $todayT = ($checkToDay ? (DATA_CONTROLLO_ATTUALE != "" ? new DateTime(DATA_CONTROLLO_ATTUALE) : new DateTime()) : new DateTime($dataFine) );
                if ($from <= $todayF && $from >= $birthdayF && $to <= $todayT && $to >= $birthdayT) {
                    $response['esito'] = 1;
                } else {
                    if ($from > $todayF) {
                        $response['erroreDescrizione'] = "Attenzione, la <b>data inizio</b> è successiva alla data odierna!";
                    } else if ($from < $birthdayF) {
                        $response['erroreDescrizione'] = "Attenzione, la <b>data inizio</b> è precedente alla tua data di nascita!";
                    } else if ($to > $todayT) {
                        $response['erroreDescrizione'] = "Attenzione, la <b>data fine</b> è successiva alla data odierna!";
                    } else if ($to < $birthdayT) {
                        $response['erroreDescrizione'] = "Attenzione, la <b>data fine</b> è precedente alla tua data di nascita!";
                    } else {
                        $response['erroreDescrizione'] = "Attenzione, errore in una data!";
                    }
                }
            } else {
                $response['erroreDescrizione'] = "<b>Attenzione la data inizio (" . $dataInizio . ") del periodo è successiva alla data di fine (" . $dataFine . ") periodo!</b>";
            }
        }
        return $response;
    }

    /**
     * Funzione per la suddivisione di un periodo tra due date in Anni Mesi e Giorni 
     * @param type $data_inizio
     * @param type $data_fine
     * @param type $addDay (se impostato a true aggiunge un giorno alla data finale per calcolare l'estremo del periodo)
     * @param type $roudMonth (se impostato a true in caso di giorni superiori o uguali a 30 arrotonda al mese)
     * @return int
     */
    public static function getPeriodoAnniMesiGiorni($data_inizio = null, $data_fine = null, $addDay = true, $roudMonth = true) {
        $response = array();
        $response['ANNI'] = 0;
        $response['MESI'] = 0;
        $response['GIORNI'] = 0;
        if (!empty($data_inizio) && !empty($data_fine)) {
            $inizio = new DateTime($data_inizio);
            $fine = new DateTime($data_fine);
            if ($addDay) {
                $fine->add(new DateInterval('P1D'));
            }

            $dif = $inizio->diff($fine);

            $anni = $dif->y;
            $mesi = $dif->m;
            $giorni = $dif->d;

            if ($roudMonth) {
                if (intval($giorni) == 30) {
                    $giorni = 0;
                    $mesi = $mesi + 1;
                    if ($mesi == 12) {
                        $mesi = 0;
                        $anni = $anni + 1;
                    }
                }
            }

            //$response['diff'] = $diff_giorni;
            //$response['ULTIMO_DEL_MESE'] = $coeff;
            $response['ANNI'] = $anni;
            $response['MESI'] = $mesi;
            $response['GIORNI'] = $giorni;
        }
        return $response;
    }

}
