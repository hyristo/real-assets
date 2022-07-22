<?php

define("DATE_FORMAT_ITA", 1);
define("DATE_FORMAT_ISO", 2);
define("DATE_FORMAT_ITA_WITHOUT_SEP", 3);
define("ATTIVA_NAVBAR", false);
define("ATTIVA_MENUBAR", true);

/* ERRORI METODO GOOGLE */
define("EMAIL_EXISTS", "Email già registrata");
define("OPERATION_NOT_ALLOWED", "Operazione non permessa");
define("TOO_MANY_ATTEMPTS_TRY_LATER", "Operazione bloccata a causa di diversi tentativi inusuali da questo dispositivo");
define("EMAIL_NOT_FOUND", "Email non presente");
define("INVALID_PASSWORD", "Password errata");
define("USER_DISABLED", "Utente Disabilitato");
define("INVALID_ID_TOKEN", "E' necessario rieseguire l'autenticazione al sistema");
define("USER_NOT_FOUND", "Utente non trovato");
define("WEAK_PASSWORD", "La password deve essere di almeno 8 caratteri");

define("LUNGHEZZA_CAP", 5);
define("LUNGHEZZA_INDIRIZZO", 150);
define("CONTROLLO_BIRTHDATE", false); // Attiva il controlla sulla data di nascita (Non si possono inserire date inferiori alla data di nascita dell'utente loggato)
define("CONTROLLO_TODATE", false); // Attiva il controlla sulla data di oggi (Non si possono inserire date superiori alla data DATA_CONTROLLO_ATTUALE )

define("DATA_CONTROLLO_ATTUALE", date("Y-m-d"));

/*
  ########################
  ## GRUPPI CODICI VARI ##
  ########################
 */
$GRUPPI_CODICIVARI = array(
    "TIPO_PERSONA" => "Tipo persona",
    "SESSO" => "Sesso",
    "TIPO_PATRIMONIO" => "Tipo patrimonio",
    "TIPO_RECAPITO" => "Tipo recapito",
    "TIPO_ALERT" => "Tipo avviso",
    "TIPO_RATA" => "Tipo rata"
);
