<?php
/* PATH */
define("ROOT_UPLOAD", ROOT . "upload/");
define("ROOT_IMPORT", ROOT . "import/");
define("UPLOAD_FOTO", "foto/");
define("ROOT_UPLOAD_FOTO", ROOT_UPLOAD . UPLOAD_FOTO);
define("ROOT_UPLOAD_PHOTO", "photo/");
define("UPLOAD_ALLEGATI", "allegati/");
define("ROOT_UPLOAD_DOCUMENTI", ROOT_UPLOAD . UPLOAD_ALLEGATI);
define("ROOT_UPLOAD_DOC", "doc/");
$ESTENSIONI_FOTO = array('jpg', 'jpeg', 'png',);
$ESTENSIONI_ALLEGATI = array('pdf', 'doc', 'docx', 'odt');
define("ESTNSIONI_FOTO_TXT", ".jpg, .jpeg, .png");
define("ESTNSIONI_ALLEGATI_TXT", ".pdf, .doc, .docx, .odt");

//define("ROOT_UPLOAD_PREPARAZIONE", ROOT_UPLOAD . "preparazione/");
//define("ROOT_UPLOAD_PREPARAZIONE_TMP", ROOT_UPLOAD . "preparazionetmp/");
//define("ROOT_UPLOAD_EROGAZIONE", ROOT_UPLOAD . "erogazione/");
//define("ROOT_UPLOAD_EROGAZIONE_TMP", ROOT_UPLOAD . "erogazionetmp/");

/* SIZE */
define("MAX_NUM_UPLOAD_OPZ", 1); //MAX Numero file opzionali caricabili
define("MAX_FILE_SIZE", 5242880); // 5242880 -> 5 MB // 20971520 -> 20MB // 31457280 -> 30MB
define("MAX_SIZE_UPLOAD_OPZ", MAX_FILE_SIZE);
define("MAX_SIZE_UPLOAD", MAX_FILE_SIZE);
define("MAX_SIZE_FILE", MAX_FILE_SIZE);
define("MAX_SIZE_FILE_P7M", MAX_FILE_SIZE);
define("MAX_SIZE_FILE_UPLOAD", MAX_SIZE_FILE);
define("MAX_SIZE_FILE_TEXT", 5);

/*  ################## GESTIONE CONTROLLI SU FILE p7M ################### */
define("DEMO_UPLOAD_P7M", 0); // Disable check upload file p7m
define('DISABLED_HASH_CONTROL', false); // se impostata a true viene omesso il controllo sull'hash del file pdf
define('SBLOCCO_UPLOAD_FILE_P7M', false); // se impostata a true vengono bypassati tutti i controlli relativi alla verifica del file p7m
$CODICI_ERRORE_UPLOAD_P7M[202079] = "Il codice fiscale del soggetto firmatario del certificato di firma non è corretto";
$CODICI_ERRORE_UPLOAD_P7M[2020100] = "Certificato di firma digitale scaduto";
$CODICI_ERRORE_UPLOAD_P7M[2020276] = "Al momento non è possibile completare il processo di verifica del file firmato";
$CODICI_ERRORE_UPLOAD_P7M[2020277] = "Al momento non è possibile estrarre il contenuto del file firmato";
$CODICI_ERRORE_UPLOAD_P7M[2020280] = "L'hash del file caricato non corrisponde all'hash del file estratto dalla piattaforma. Esportare nuovamente il file nel caso la domanda sia stata oggetto di modifica o nel caso in cui sia stato nuovamente esportato il file pdf.";
$codiciErroreNoControll = array(202079, 2020100, 2020276, 2020280); // se SBLOCCO_UPLOAD_FILE_P7M è impostata a true vengono bypassati i controlli per i codici di errore inseriti nell'array

/* TIPI RICHISTA FILE SIZE (MODULI FASE COMPLETAMENTO DOMANDA) */
$TIPO_RICHIESTA_FILESIZE = array(
    0 => "5",
    1 => "5",
    2 => "10",
    3 => "20"
);

/* DEFINIZIONE DELLE FASI PER LA TABELLA DOCUMENTI BANDO */
define('DOCUMENTI_FASE_1' ,1);
define('DOCUMENTI_FASE_2' ,2);
define('DOCUMENTI_FASE_3' ,3);

/* DEFINIZIONE DEI TIPI DI DOCUMENTO CARICATI IN TABELLA ALLEGATI */
define('TIPO_UPLOAD_OBBLIGATORIO' ,1);
define('TIPO_UPLOAD_OPZIONALE' ,2);
define('TIPO_UPLOAD_ISTANZA' ,3);
define('TIPO_UPLOAD_CONTRIBUTO' ,4);

?>