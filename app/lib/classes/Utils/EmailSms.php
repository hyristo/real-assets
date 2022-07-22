<?

class EmailSms {

    public static function sanitizeEmail($email = "") {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    public static function checkEmail($email = "") {
        $response = false;
        $email = SELF::sanitizeEmail($email);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = true;
        }
        return $response;
    }

    public static function checkEmailDomain($input, $domain = PERMITTED_EMAIL_DOMAIN) {
        return Utils::stringEndsWith($input, $domain);
    }

    /*
     * funzione di invio sms
     */

    function sendsmsOtp($otp, $numerosms) {
        $userSms = $GLOBALS["SMS_CONFIG"]['USERSMS'];
        $pwdSms = $GLOBALS["SMS_CONFIG"]['PWDSMS'];
        $urlSms = $GLOBALS["SMS_CONFIG"]['URLSMS'];
        $senderSms = $GLOBALS["SMS_CONFIG"]['SENDERSMS']; //MAX 10CHAR
        $testoSms = "Sipars - OTP :" . $otp;

        $postfields = array(
            smsUSER => $userSms,
            smsPASSWORD => $pwdSms,
            smsTEXT => $testoSms,
            smsSENDER => $senderSms,
            smsGATEWAY => "M",
//        smsNUMBER => '+39' . $numerosms);
            smsNUMBER => '+1111111111');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $urlSms);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
        $result = curl_exec($ch);
        $xml = htmlentities($result);
        $substr = '+Ok';
        if (strpos($xml, $substr) !== false) {
            return 1;
        } else {
            return 0;
        }
    }

    /*
     * Creazione Otp
     */

    public static function createOtp() {
        $otp = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
        return $otp;
    }

    /**
     * Funzione dell'invio dell'email dopo la registrazione dell'utente
     * @param type $email
     * @param type $username
     * @param type $password
     * @param type $codiceGenerato
     * @return int
     */
    public static function invioMailRegUtente($email, $username, $password, $codiceGenerato) {

        if (!INVIO_EMAIL)
            return 1;

        $testo = '<b>Benvenuto nel portale ' . APP_NAME . '</b><br>
        Ecco di tuoi dati di accesso<br>
        <table style="width: 65%;">
            <tbody>
                <tr>
                    <td  width="20%">Link di accesso:</td>
                    <td width="80%"> <b>' . BASE_HTTP . '</b></td>
                </tr>            
                <tr>
                    <td  width="20%">Username:</td>
                    <td width="80%"> <b>' . $username . '</b></td>
                </tr>
                <tr>
                    <td width="20%">Password:</td>
                    <td width="80%"><b>' . $password . '</b></td>
                </tr>
                <tr>
                    <td width="20%"> Codice Da inserire:</td>
                    <td width="80%"><b>' . $codiceGenerato . '</b></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td><img src="' . BASE_HTTP . 'app-assets/images/logo/logoFV.png"></td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
        ';

        date_default_timezone_set('Etc/UTC');
        $mail = new PHPMailer;
        $mail->IsSMTP();
        $mail->CharSet = "UTF-8";
        $mail->SMTPDebug = $GLOBALS['EMAIL_CONFIG']['DEBUG'];
        $mail->Debugoutput = 'html';
        $mail->Host = $GLOBALS['EMAIL_CONFIG']['HOST'];
        $mail->Port = $GLOBALS['EMAIL_CONFIG']['PORT'];
        $mail->SMTPSecure = $GLOBALS['EMAIL_CONFIG']['SMTP_SECURE'];
        $mail->SMTPAuth = $GLOBALS['EMAIL_CONFIG']['SMTP_AUTH'];
        $mail->Username = $GLOBALS['EMAIL_CONFIG']['USERNAME'];
        $mail->Password = $GLOBALS['EMAIL_CONFIG']['PASSWORD'];
        $mail->setFrom($GLOBALS['EMAIL_CONFIG']['FROM'], $GLOBALS['EMAIL_CONFIG']['FROM_DESC']);
        $mail->addAddress($email, $email);
        $mail->Subject = $GLOBALS['EMAIL_CONFIG']['SUBJECT'];
        $mail->msgHTML($testo);
        $mail->AltBody = $testo;
        if (!$mail->send()) {
            $value = "Mailer Error: " . $mail->ErrorInfo;
            Utils::print_array($value);
            $value = 0;
        } else {
            $value = 1;
        }
        return $value;
    }

    /**
     * Funzione per l'invio delle email dopo la richiesta di reset password dell'utente
     * @param type $email
     * @param type $username
     * @param type $password
     * @return int
     */
    public static function invioMailResetPswUtente($email, $username, $password) {

        if (!INVIO_EMAIL)
            return 1;

        $testo = '<b>' . APP_NAME . '</b><br>
        La password è stata modificata<br>
        di seguito i dati di accesso<br>
        <table style="width: 65%;">
            <tbody>
                <tr>
                    <td  width="20%">Link di accesso:</td>
                    <td width="80%"> <b>' . BASE_HTTP . '</b></td>
                </tr>            
                <tr>
                    <td  width="20%">Username:</td>
                    <td width="80%"> <b>' . $username . '</b></td>
                </tr>
                <tr>
                    <td width="20%">Password:</td>
                    <td width="80%"><b>' . $password . '</b></td>
                </tr>                
            </tbody>
        </table>
        <br>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td><img src="' . BASE_HTTP . 'app-assets/images/logo/logoFV.png"></td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
        ';

        date_default_timezone_set('Etc/UTC');
        $mail = new PHPMailer;
        $mail->IsSMTP();
        $mail->CharSet = "UTF-8";
        $mail->SMTPDebug = $GLOBALS['EMAIL_CONFIG']['DEBUG'];
        $mail->Debugoutput = 'html';
        $mail->Host = $GLOBALS['EMAIL_CONFIG']['HOST'];
        $mail->Port = $GLOBALS['EMAIL_CONFIG']['PORT'];
        $mail->SMTPSecure = $GLOBALS['EMAIL_CONFIG']['SMTP_SECURE'];
        $mail->SMTPAuth = $GLOBALS['EMAIL_CONFIG']['SMTP_AUTH'];
        $mail->Username = $GLOBALS['EMAIL_CONFIG']['USERNAME'];
        $mail->Password = $GLOBALS['EMAIL_CONFIG']['PASSWORD'];
        $mail->setFrom($GLOBALS['EMAIL_CONFIG']['FROM'], $GLOBALS['EMAIL_CONFIG']['FROM_DESC']);
        $mail->addAddress($email, $email);
        $mail->Subject = $GLOBALS['EMAIL_CONFIG']['SUBJECT'];
        $mail->msgHTML($testo);
        $mail->AltBody = $testo;
        if (!$mail->send()) {
            $value = "Mailer Error: " . $mail->ErrorInfo;
            Utils::print_array($value);
            $value = 0;
        } else {
            $value = 1;
        }
        return $value;
    }

    /**
     * Funzione invio email per la registrazione di un nuovo operatore
     * @global type $GLOBALS
     * @param type $email
     * @param type $username
     * @param type $password
     * @return int
     */
    public static function invioMailRegOperatore($email, $username, $password) {
        global $GLOBALS;

        if (!INVIO_EMAIL) {
            $value['esito'] = 1;
            return $value;
        }

        $testo = '<b>Benvenuto nel portale ' . APP_NAME . '</b><br>
            Ecco di tuoi dati di accesso<br>
            <table style="width: 65%;">
                <tbody>
                    <tr>
                        <td  width="20%">Link di accesso:</td>
                        <td width="80%"> <b>' . BASE_HTTP . 'admin/</b></td>
                    </tr>            
                    <tr>
                        <td  width="20%">Username:</td>
                        <td width="80%"> <b>' . $username . '</b></td>
                    </tr>
                    <tr>
                        <td width="20%">Password:</td>
                        <td width="80%"><b>' . $password . '</b></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table style="width: 100%;">
                <tbody>
                    <tr>
                        <td>&nbsp;</td>
                        <td><img src="' . BASE_HTTP . 'app-assets/images/logo/logoFV.png"></td>
                        <td>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
        ';
        date_default_timezone_set('Etc/UTC');
        $mail = new PHPMailer;
        $mail->IsSMTP();
        $mail->CharSet = "UTF-8";
        $mail->SMTPDebug = $GLOBALS['EMAIL_CONFIG']['DEBUG'];
        $mail->Debugoutput = 'html';
        $mail->Host = $GLOBALS['EMAIL_CONFIG']['HOST'];
        $mail->Port = $GLOBALS['EMAIL_CONFIG']['PORT'];
        $mail->SMTPSecure = $GLOBALS['EMAIL_CONFIG']['SMTP_SECURE'];
        $mail->SMTPAuth = $GLOBALS['EMAIL_CONFIG']['SMTP_AUTH'];
        $mail->Username = $GLOBALS['EMAIL_CONFIG']['USERNAME'];
        $mail->Password = $GLOBALS['EMAIL_CONFIG']['PASSWORD'];
        $mail->setFrom($GLOBALS['EMAIL_CONFIG']['FROM'], $GLOBALS['EMAIL_CONFIG']['FROM_DESC']);
        $mail->addAddress($email, $email);
        $mail->Subject = $GLOBALS['EMAIL_CONFIG']['SUBJECT'];
        $mail->msgHTML($testo);
        $mail->AltBody = $testo;
        if (!$mail->send()) {
            $value['descrizioneErrore'] = "Mailer Error: " . $mail->ErrorInfo;
            $value['esito'] = 0;
        } else {
            $value['esito'] = 1;
        }
        return $value;
    }

    /**
     * Funzione dell'invio della mei l dopo la richiesta di recupero password 
     * @param type $email
     * @param type $username
     * @param type $password
     * @return int
     */
    public static function invioMailResetPswOperatore($email, $username, $password) {

        if (!INVIO_EMAIL)
            return 1;

        $testo = '<b>' . APP_NAME . '</b><br>
        La password è stata modificata<br>
        di seguito i dati di accesso<br>
        <table style="width: 65%;">
            <tbody>
                <tr>
                    <td  width="20%">Link di accesso:</td>
                    <td width="80%"> <b>' . BASE_HTTP . 'admin/</b></td>
                </tr>            
                <tr>
                    <td  width="20%">Username:</td>
                    <td width="80%"> <b>' . $username . '</b></td>
                </tr>
                <tr>
                    <td width="20%">Password:</td>
                    <td width="80%"><b>' . $password . '</b></td>
                </tr>                
            </tbody>
        </table>
        <br>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td><img src="' . BASE_HTTP . 'app-assets/images/logo/logoFV.png"></td>
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>
        ';

        date_default_timezone_set('Etc/UTC');
        $mail = new PHPMailer;
        $mail->IsSMTP();
        $mail->CharSet = "UTF-8";
        $mail->SMTPDebug = $GLOBALS['EMAIL_CONFIG']['DEBUG'];
        $mail->Debugoutput = 'html';
        $mail->Host = $GLOBALS['EMAIL_CONFIG']['HOST'];
        $mail->Port = $GLOBALS['EMAIL_CONFIG']['PORT'];
        $mail->SMTPSecure = $GLOBALS['EMAIL_CONFIG']['SMTP_SECURE'];
        $mail->SMTPAuth = $GLOBALS['EMAIL_CONFIG']['SMTP_AUTH'];
        $mail->Username = $GLOBALS['EMAIL_CONFIG']['USERNAME'];
        $mail->Password = $GLOBALS['EMAIL_CONFIG']['PASSWORD'];
        $mail->setFrom($GLOBALS['EMAIL_CONFIG']['FROM'], $GLOBALS['EMAIL_CONFIG']['FROM_DESC']);
        $mail->addAddress($email, $email);
        $mail->Subject = $GLOBALS['EMAIL_CONFIG']['SUBJECT'];
        $mail->msgHTML($testo);
        $mail->AltBody = $testo;
        if (!$mail->send()) {
            $value = "Mailer Error: " . $mail->ErrorInfo;
            Utils::print_array($value);
            $value = 0;
        } else {
            $value = 1;
        }
        return $value;
    }

    /*
     * Mail di avvenuto caricamento del file p7m(Documento Finale firmato)
     *  a completamento della domanda 
     * Semplice mail che indica il corretto invio della pratica
     */

    public static function invioMailPresentazioneDomanda($codice_numerico, $data_presentazione, $email, $desc_bando) {
        global $LoggedAccount;
        if (!INVIO_EMAIL) {
            return 1;
        } else {
            $url_img = BASE_HTTP . "assets/img/siciliafesr.png";
            if (DEMO_CERT) {
                $url_img = "https://demo.fitosan.igea-lab.it/siciliafesr.png";
            }
            $data_presentazione = Utils::dateOracleTimeStamp($data_presentazione, 'd-m-Y H:i');
            $anno = date('Ym', strtotime($data_presentazione));
            $codice_numerico = $anno . "" . str_pad($codice_numerico, 5, "0", STR_PAD_LEFT);
            // $testo = '<b>' . APP_NAME . '</b><br><br>
            $testo = 'Gentilissimo, <br><br>la procedura di inoltro della domanda al bando <b>' . $desc_bando . '</b>

        è avvenuta con successo.<br>
        Di seguito il riepilogo dei dati:
        <table style="width: 50%;">
            <tbody>
               <tr>
                    <td width="30%">Denominazione Istituto:</td>
                    <td width="50%"><b>' . $LoggedAccount->DENOMINAZIONE . '</b></td>
                </tr>     
                <tr>
                    <td width="30%">Codice Meccanografico:</td>
                    <td width="50%"> <b>' . $LoggedAccount->CODICE_MECCANOGRAFICO . '</b></td>
                </tr>
                <tr>
                    <td width="30%">Numero presentazione:</td>
                    <td width="50%"> <b>' . $codice_numerico . '</b></td>
                </tr>
                <tr>
                    <td width="30%">Data presentazione:</td>
                    <td width="50%"> <b>' . $data_presentazione . '</b></td>
                </tr>
            </tbody>
        </table>
        <br>

        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td><img width="568" src="' . $url_img . '" alt="Regione Siciliana - PO FESR Sicilia 2014-2020"/></td>                    
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>';
            date_default_timezone_set('Etc/UTC');
            $mail = new PHPMailer;
            $mail->IsSMTP();
            $mail->CharSet = "UTF-8";
            $mail->SMTPDebug = $GLOBALS['EMAIL_CONFIG']['DEBUG'];
            $mail->Debugoutput = 'html';
            $mail->Host = $GLOBALS['EMAIL_CONFIG']['HOST'];
            $mail->Port = $GLOBALS['EMAIL_CONFIG']['PORT'];
            $mail->SMTPSecure = $GLOBALS['EMAIL_CONFIG']['SMTP_SECURE'];
            $mail->SMTPAuth = $GLOBALS['EMAIL_CONFIG']['SMTP_AUTH'];
            $mail->Username = $GLOBALS['EMAIL_CONFIG']['USERNAME'];
            $mail->Password = $GLOBALS['EMAIL_CONFIG']['PASSWORD'];
            $mail->setFrom($GLOBALS['EMAIL_CONFIG']['FROM'], $GLOBALS['EMAIL_CONFIG']['FROM_DESC']);
            $mail->addAddress($email, $email);
            $mail->Subject = $GLOBALS['EMAIL_CONFIG']['SUBJECT'];
            $mail->msgHTML($testo);
            $mail->AltBody = $testo;
            if (!$mail->send()) {
                $value = "Mailer Error: " . $mail->ErrorInfo;
                Utils::print_array($value);
                $value = 0;
            } else {
                $value = 1;
            }
        }
        return $value;
    }

    public static function invioMailPresentazione($domanda) {
        global $con, $LoggedAccount;
        if (!INVIO_EMAIL) {
            return 1;
        } else {
            $istituto = new AccountIstituto($domanda->CODICE_ISTITUTO);
            $avviso = new Bando($domanda->ID_BANDO);
            $email = $istituto->EMAIL;
            $url_img = BASE_HTTP . "assets/img/web_loghi.png";
            // $testo = '<b>' . APP_NAME . '</b><br><br>
            $testo = 'Gentilissimo, <br><br>la procedura di presentazione relativamente a <b>' . DESCRIZIONE_BANDO_MAIL . '</b>
        è avvenuta con successo.<br>
        Di seguito il riepilogo dei dati:
        <table style="width: 100%;">
            <tbody>
               <tr>
                    <td width="30%">Denominazione Istituto:</td>
                    <td width="70%"><b>' . $istituto->DENOMINAZIONE . '</b></td>
                </tr>     
                <tr>
                    <td width="30%">Codice Meccanografico:</td>
                    <td width="70%"> <b>' . $istituto->CODICE_MECCANOGRAFICO . '</b></td>
                </tr>
                <tr>
                    <td width="30%">Avviso:</td>
                    <td width="70%"> <b>' . $avviso->DESCRIZIONE . '</b></td>
                </tr>
                <tr>
                    <td width="30%">Numero protocollo interno:</td>
                    <td width="70%"> <b>' . $domanda->PROTOCOLLO . '</b></td>
                </tr>
                <tr>
                    <td width="30%">Data emissione protocollo:</td>
                    <td width="70%"> <b>' . Date::dateOracleTimeStamp($domanda->DATA_PROTOCOLLO, "d-m-Y H:i:s") . '</b></td>
                </tr>
            </tbody>
        </table>
        <br>
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td><img width="568" src="' . $url_img . '" alt="Regione Siciliana - PO FESR Sicilia 2014-2020"/></td>                    
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>';
            date_default_timezone_set('Etc/UTC');
            $mail = new PHPMailer;
            $mail->IsSMTP();
            $mail->CharSet = "UTF-8";
            $mail->SMTPDebug = $GLOBALS['EMAIL_CONFIG']['DEBUG'];
            $mail->Debugoutput = 'html';
            $mail->Host = $GLOBALS['EMAIL_CONFIG']['HOST'];
            $mail->Port = $GLOBALS['EMAIL_CONFIG']['PORT'];
            $mail->SMTPSecure = $GLOBALS['EMAIL_CONFIG']['SMTP_SECURE'];
            $mail->SMTPAuth = $GLOBALS['EMAIL_CONFIG']['SMTP_AUTH'];
            $mail->Username = $GLOBALS['EMAIL_CONFIG']['USERNAME'];
            $mail->Password = $GLOBALS['EMAIL_CONFIG']['PASSWORD'];
            $mail->setFrom($GLOBALS['EMAIL_CONFIG']['FROM'], $GLOBALS['EMAIL_CONFIG']['FROM_DESC']);
            $mail->addAddress($email, $email);
            $mail->Subject = $GLOBALS['EMAIL_CONFIG']['SUBJECT'];
            $mail->msgHTML($testo);
            $mail->AltBody = $testo;
            if (!$mail->send()) {
                $value = "Mailer Error: " . $mail->ErrorInfo;
//            Utils::print_array($value);
                $value = 0;
            } else {
                $value = 1;
            }
        }
        return $value;
    }

    public static function invioMailManifestazioneInteresse($domanda) {
        global $con, $LoggedAccount;
        if (!INVIO_EMAIL)
            return 1;

//        $date_str = explode("-", $data_presentazione);
//        $anno = explode(" ", $date_str[2]);
//
//        $splitto_anno = $date_str[0] . "-" . $date_str[1] . "-" . $anno[0];
//        print_r($splitto_anno);
//        echo date("Y-m-d", strtotime($splitto_anno));    
        $istituto = new AccountIstituto($domanda->CODICE_ISTITUTO);
        $avviso = new Bando($domanda->ID_BANDO);
        $email = $istituto->EMAIL;
        $codice_numerico = str_pad($domanda->ID, 6, "0", STR_PAD_LEFT);

        $url_img = BASE_HTTP . "assets/img/web_loghi.png";

        // $testo = '<b>' . APP_NAME . '</b><br><br>
        $testo = 'Gentilissimo/a , <br><br>la procedura di presentazione relativamente a <b>' . DESCRIZIONE_BANDO_MAIL . '</b>

        è avvenuta con successo.<br>
        Di seguito il riepilogo dei dati:
        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td width="30%">Denominazione Istituto:</td>
                    <td width="70%"><b>' . $istituto->DENOMINAZIONE . '</b></td>
                </tr>     
                <tr>
                    <td width="30%">Codice Meccanografico:</td>
                    <td width="70%"> <b>' . $istituto->CODICE_MECCANOGRAFICO . '</b></td>
                </tr>
                <tr>
                    <td width="30%">Avviso:</td>
                    <td width="70%"> <b>' . $avviso->DESCRIZIONE . '</b></td>
                </tr>
                <tr>
                    <td width="30%">Numero partecipazione:</td>
                    <td width="70%"> <b>' . $codice_numerico . '</b></td>
                </tr>
            </tbody>
        </table>
        <br>

        <table style="width: 100%;">
            <tbody>
                <tr>
                    <td>&nbsp;</td>
                    <td><img width="568" src="' . $url_img . '" alt="' . APP_NAME . '"/></td>                    
                    <td>&nbsp;</td>
                </tr>
            </tbody>
        </table>';
        // <td><img src="' . BASE_HTTP . 'assets/img/siciliafesr.png" alt="Regione Siciliana - PO FESR Sicilia 2014-2020"/></td>


        date_default_timezone_set('Etc/UTC');
        $mail = new PHPMailer;
        $mail->IsSMTP();
        $mail->CharSet = "UTF-8";
        $mail->SMTPDebug = $GLOBALS['EMAIL_CONFIG']['DEBUG'];
        $mail->Debugoutput = 'html';
        $mail->Host = $GLOBALS['EMAIL_CONFIG']['HOST'];
        $mail->Port = $GLOBALS['EMAIL_CONFIG']['PORT'];
        $mail->SMTPSecure = $GLOBALS['EMAIL_CONFIG']['SMTP_SECURE'];
        $mail->SMTPAuth = $GLOBALS['EMAIL_CONFIG']['SMTP_AUTH'];
        $mail->Username = $GLOBALS['EMAIL_CONFIG']['USERNAME'];
        $mail->Password = $GLOBALS['EMAIL_CONFIG']['PASSWORD'];
        $mail->setFrom($GLOBALS['EMAIL_CONFIG']['FROM'], $GLOBALS['EMAIL_CONFIG']['FROM_DESC']);
        $mail->addAddress($email, $email);
        $mail->Subject = $GLOBALS['EMAIL_CONFIG']['SUBJECT'];
        $mail->msgHTML($testo);
        $mail->AltBody = $testo;
        if (!$mail->send()) {
            $value = "Mailer Error: " . $mail->ErrorInfo;
//            Utils::print_array($value);
            $value = 0;
        } else {
            $value = 1;
        }
        return $value;
    }

}
