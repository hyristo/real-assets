<?php

class File {

    public static function GetValidName($oldName) {
        $newName = "";
        $name = $oldName;
        for ($i = 0; $i < strlen($name); $i++) {
            $char = substr($name, $i, 1);
            if (!preg_match("/^[a-zA-Z0-9]$/", $char))
                $char = "_";
            $newName .= $char;
        }
        return $newName;
    }

    /**
     * 
     * @param type $oldName
     * @param type $newExt
     * @param type $arrayExt estensioni da controllare di secondo livello nel filename es: array('.pdf','.doc','.zip')
     * @return type
     */
    public static function GetValidFilename($oldName, $newExt = "", $arrayExt = array('.pdf')) {
        $newName = "";
        $name = $oldName;
        $ext = self::GetFilenameExtension($name);
        if ($ext != "") {
            $name = substr($name, 0, strlen($name) - strlen($ext));
            $ext2 = self::GetFilenameExtension($name);
            if (in_array($ext2, $arrayExt)) {
                $name = substr($name, 0, strlen($name) - strlen($ext2));
                $ext = $ext2 . $ext;
            }
        }
        for ($i = 0; $i < strlen($name); $i++) {
            $char = substr($name, $i, 1);
            if (!preg_match("/^[a-zA-Z0-9-]$/", $char))
                $char = "_";
            $newName .= $char;
        }
        $newName = str_replace(" ", "_", $newName);
        $newName .= ($newExt != "" ? $newExt : $ext);
        return $newName;
    }

    public static function GetFilenameExtension($filename) {
        $i = strrpos($filename, ".");
        if ($i === false)
            return "";
        return substr($filename, $i);
    }

    /**
     * Verifico se il certificato del file firmato p7m è valido e se il codice fiscale del firmatario corrisponde al codice fiscale dell'utente loggato o del codice fiscale passato per riferimento
     * @global type $LoggedAccount
     * @param type $leggoCertificato
     * @param type $codicefiscale
     * @return string
     */
    public static function verifyCertificato($leggoCertificato, $codicefiscale = '') {
        global $LoggedAccount, $CODICI_ERRORE_UPLOAD_P7M;
        $return = Utils::initDefaultResponse();
        $cfiscaleFirmatario = ($codicefiscale != "" ? $codicefiscale : $LoggedAccount->CODICE_FISCALE);
        $cerco = Utils::tag_contents($leggoCertificato, "-----BEGIN CERTIFICATE-----", "-----END CERTIFICATE-----");
        foreach ($cerco as $scorrolefirme) {
            $datiCertificatoSearch = openssl_x509_parse("-----BEGIN CERTIFICATE-----\n" . trim($scorrolefirme) . "\n-----END CERTIFICATE-----");
            //echo self::extractCf($datiCertificatoSearch['subject']['serialNumber']); exit();
            if ($cfiscaleFirmatario == self::extractCf($datiCertificatoSearch['subject']['serialNumber'])) {
                $verifyValidCertificateDate = self::verifyValidCertificateDate($datiCertificatoSearch['validFrom_time_t'], $datiCertificatoSearch['validTo_time_t']);
                if ($verifyValidCertificateDate['esito'] == 1) {
                    $return['esito'] = 1;
                    $return['descrizioneErrore'] = "";
                } else {
                    $return = $verifyValidCertificateDate;
                }
                return $return;
            } else {
                $return['esito'] = 202079;
                $return['descrizioneErrore'] = $CODICI_ERRORE_UPLOAD_P7M[202079];
            }
        }

        return $return;
    }

    /**
     * Verifica la scadenza del certificato p7m
     * @param type $validFrom
     * @param type $validTo
     * @return string
     */
    public static function verifyValidCertificateDate($validFrom, $validTo) {
        global $CODICI_ERRORE_UPLOAD_P7M;
        $dataAttuale = time();
        $return = Utils::initDefaultResponse();
        if ($dataAttuale >= $validFrom && $dataAttuale <= $validTo) {
            $return['esito'] = 1;
            $return['descrizioneErrore'] = "";
        } else {
            $return['esito'] = 2020100;
            $return['descrizioneErrore'] = $CODICI_ERRORE_UPLOAD_P7M[2020100];
        }
        return $return;
    }

    public static function verifyCertificatoRevisore($datiCertificato = "") {
        global $LoggedAccount;
        $dataAttuale = time();
        $validFrom = $datiCertificato['validFrom_time_t'];
        $validTo = $datiCertificato['validTo_time_t'];
        if ($dataAttuale >= $validFrom && $dataAttuale <= $validTo) {
            $return['esito'] = 1;
        } else {
            $return['esito'] = -999;
            $return['descrizioneErrore'] = "Certificato di firma digitale scaduto";
        }
        return $return;
    }

    /**
     * Estrai li codice fiscale dal tag del certificato p7m
     * @param type $codice_fiscale
     * @return type
     */
    public static function extractCf($codice_fiscale) {
        $cfrevisitedexplode = explode("-", $codice_fiscale);
        if ($cfrevisitedexplode[1] == "") {
            $cfrevisitedexplode = explode(":", $codice_fiscale);
            $cfrevisited = $cfrevisitedexplode[1];
        } else {
            $cfrevisited = $cfrevisitedexplode[1];
        }
        return $cfrevisited;
    }

    /**
     * Funzioni controllo file
     */
    /*     * *********************************** TEST GIGI ************************************************** */
    public static function checkFileAllegato($uploadedName = '', $max_file_size = MAX_FILE_SIZE, $format = array('p7m')) {
        $response = Utils::initDefaultResponse();
        if ($uploadedName != '') {
            if (intval($_FILES[$uploadedName]['size']) <= 0) {
                $response['erroreDescrizione'] = "Dimensione file non consistente";
            } else if (intval($_FILES[$uploadedName]['size']) > $max_file_size) {
                $response['erroreDescrizione'] = "Dimensione file non consentita";
            } else if ($_FILES[$uploadedName]['tmp_name'] == "") {
                $response['erroreDescrizione'] = "Nessun file allegato";
            } else {
                $ext = self::extractExtensionFromFile($uploadedName);
                if (empty($ext) || !in_array($ext, $format)) {
                    $response['erroreDescrizione'] = "Formato del file non valido!";
                } else {
                    $response['esito'] = 1;
                }
            }
        }
        return $response;
    }

    /**
     * Verifico se le dimensioni e MD5 del file temporaneo sono uguali al file copiato
     * @param type $tmp_name
     * @param type $filePath
     * @return type
     */
    public static function checkMd5File($tmp_name = '', $filePath = '') {
        $response = false;
        if ($tmp_name != '' && $filePath != '') {
            $response = (filesize($tmp_name) == filesize($filePath) && md5_file($tmp_name) == md5_file($filePath));
        }
        return $response;
    }

    public static function checkFileConsistency($filePath = '') {
        $response = false;
        if ($filePath != '') {
            $response = (file_exists($filePath) && filesize($filePath) > 0);
        }
        return $response;
    }

    public static function extractExtensionFromFile($uploadedName = '') {
        $response = "";
        if ($uploadedName != '' && isset($_FILES[$uploadedName]) && !empty($_FILES[$uploadedName]['name'])) {
            $ricavoExt = $_FILES[$uploadedName]['name'];
            $file = pathinfo($_FILES[$uploadedName]['name']);
            $response = strtolower($file['extension']);
        }
        return $response;
    }

    public static function getValidFilenameFromFile($uploadedName = '') {
        $response = "";
        if ($uploadedName != "" && isset($_FILES[$uploadedName])) {
            $response = self::GetValidFilename($_FILES[$uploadedName]['name']);
        }
        return $response;
    }

    public static function getFolderFromDomanda($domanda = null, $root_upload = '') {
        $folder = '';
        if (!empty($domanda) && !empty($domanda->ID) && !empty($domanda->CODICE_FISCALE) && !empty($root_upload)) {
            $folder = $root_upload . $domanda->ID . "_" . $domanda->CODICE_FISCALE;
        }
        return $folder;
    }

//    public static function createFolderFromDomanda($domanda = null) {
//        $folder = '';
//        $root_upload = "";
//        if (Utils::isStatoSportelloPresentazione()) {
//            $root_upload = ROOT_UPLOAD_PREPARAZIONE;
//        }
//        if (Utils::isStatoSportelloRichiesta()) {
//            $root_upload = ROOT_UPLOAD_EROGAZIONE;
//        }
//        if ($root_upload != '') {
//            if (!empty($domanda) && !empty($domanda->ID) && !empty($domanda->CODICE_ISTITUTO) && !empty($domanda->ID_BANDO) && !empty($root_upload)) {
//                $folder = $root_upload . $domanda->ID . "_" . $domanda->CODICE_ISTITUTO . "/" . $domanda->ID_BANDO . "/" . $fase;
//
//                if (is_dir($folder) === false) {
//                    if (!@mkdir($folder, 0755, true)) {
//                        $folder = "";
//                    }
//                }
//            }
//        }
//        return $folder;
//    }

    /*     * *********************************************************************************** */

    public static function maxDimension($dimensione = "", $max_file_size = 0) {
        return ($dimensione > $max_file_size ? true : false );
    }

    public static function checkTmpName($name = "") {
        $response = false;
        if ($name != "") {
            $response = ($_FILES[$name]['tmp_name'] == "" ? true : false);
        }
        return $response;
    }

    public static function extensionFile($tipo_file = "", $estenzione_file = "p7m") {
        return ($tipo_file != $estenzione_file ? true : false );
    }

    public static function md5NameFile($nomeFile = "") {
        return ($nomeFile == "" ? "" : md5($nomeFile) );
    }

    public static function createPath($path = "") {
        if (is_dir($path) === false) {
            mkdir($path, 0755, true);
        }
        if (is_dir($path) === false) { // controllo se la cartella è stata creata
            $path = '';
        }
        return $path;
    }

    public static function checkCertificatoP7m($fileSource = '', $fileDest = '', $fileDestPem = '', $codicefiscale = '', $hashfile = '') {
        global $CODICI_ERRORE_UPLOAD_P7M;
        $response = Utils::initDefaultResponse();

        $fileSourceTMPbase64 = self::setFileNamep7mTMP($fileSource);
        self::shellExecBase64($fileSource, $fileSourceTMPbase64);

        if (filesize($fileSourceTMPbase64) > 0) {
            $fileSource = $fileSourceTMPbase64;
        } else {
            unlink($fileSourceTMPbase64);
        }


        self::shellExecCms($fileSource, $fileDest);
        $hashestratto = self::md5File($fileDest);
        if (filesize($fileDest) > 0) {
            if ($hashestratto == $hashfile || DISABLED_HASH_CONTROL) {
                self::shellExecPkcs7($fileSource, $fileDestPem);
                if (filesize($fileDestPem) > 0) {
                    self::shellExecX509($fileDestPem);
                    $leggoCertificato = file_get_contents($fileDestPem);
                    $response = self::verifyCertificato($leggoCertificato, $codicefiscale);
                    if ($response['esito'] == 1) {
                        if (filesize($fileSourceTMPbase64) > 0) {
                            unlink($fileSourceTMPbase64);
                        }
                    }
                } else {
                    $response['esito'] = 2020276;
                    $response['erroreDescrizione'] = $CODICI_ERRORE_UPLOAD_P7M[2020276];
                }
            } else {
                $response['esito'] = 2020280;
                $response['erroreDescrizione'] = $CODICI_ERRORE_UPLOAD_P7M[2020280];
            }
        } else {
            $response['esito'] = 2020277;
            $response['erroreDescrizione'] = $CODICI_ERRORE_UPLOAD_P7M[2020277]; // . ($fileDest);
        }
        return $response;
    }

    /**
     * crea il nome del file temporaneo p7m per utilizzarlo nella decodifica in base64
     * @param type $fileSource
     * @return type
     */
    public static function setFileNamep7mTMP($fileSource = '') {
        $file = pathinfo($fileSource);
        return $file['dirname'] . '/FILETMP_' . $file['basename'];
    }

    public static function extractPemFromP7m() {
        
    }

    public static function shellExecCms($file = "", $fileEstratto = "") {
        return shell_exec('openssl cms -verify -noverify -in ' . $file . ' -inform DER -out ' . $fileEstratto);
    }

    public static function shellExecPkcs7($file = "", $fileEstrattoPem = "") {
        return shell_exec('openssl pkcs7 -inform DER -in ' . $file . ' -print_certs -out ' . $fileEstrattoPem);
    }

    public static function shellExecX509($fileEstrattoPem = "") {
        return shell_exec('openssl x509 -in ' . $fileEstrattoPem . ' -text -noout');
    }

    public static function md5File($nomeFile = 0) {
        return md5_file($nomeFile);
    }

    public static function shellExecBase64($file = "", $fileDest = "") {
        return shell_exec('base64 -d ' . $file . ' > ' . $fileDest);
        //return shell_exec('openssl base64 -d -in '.$file.' -out '.$fileDest);
    }

    public function generateDirForUpload($basePath = ROOT_UPLOAD_FILE, $destinationPath = '') {
        global $LoggedAccount;
        $path = '';
        if ($basePath != "") {
            $path = $basePath;
            File::createPath($path);
            if (is_dir($path)) {
                $path .= $destinationPath;
                File::createPath($path);
            } else {
                $path = '';
            }
        }
        return $path;
    }

    /**
     * in caso di errore viene fatta una copia del file p7m che ha generato l'errore e viene salvato tutto nell'apposita tabella di logs
     * @param type $fileName
     * @param type $domanda
     * @param type $fileFullPath
     * @param string $fileFullPathLogsp7m
     * @param type $errore
     * @param type $fase
     * @return type
     */
    public static function copyFileandSaveLogs($fileName = "", $domanda = null, $fileFullPath = "", $fileFullPathLogsp7m = "", $errore = "", $fase = DOCUMENTI_FASE_1) {
        $response = Utils::initDefaultResponse();
        if (!empty($fileFullPath) && !empty($fileFullPathLogsp7m) && !empty($domanda) && !empty($fileName)) {
            $folderLOGSP7M = $domanda->generateDirForUpload(ROOT_UPLOAD_PRESENTAZIONE, 'logsp7m');
            $fileFullPathLogsp7m = $folderLOGSP7M . "/" . $fileName;
            if ($folderLOGSP7M) {
                copy($fileFullPath, $fileFullPathLogsp7m);
                $logUploadP7m = new LogsP7mFile();
                $logUploadP7m->ID_DOMANDA = $domanda->ID;
                $logUploadP7m->ERRORE = $errore;
                $logUploadP7m->FILEPATH = $fileFullPathLogsp7m;
                $logUploadP7m->FASE = $fase;
                $response = $logUploadP7m->Save();
            } else {
                $response['erroreDescrizione'] = "Al momento non è possibile allegare il file, contattare un amministratore!";
            }
        }
        return $response;
    }

}
