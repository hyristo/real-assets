<?php

define('SINC_FA_STATO_SOSPESEO', 3559); //IN SOSPESO
define('SINC_FA_STATO_DEPRECATO', 2559); //DEPRECATO
define('SINC_FA_STATO_ELIMINATO', 2590);//ELIMINATO
define('SINC_FA_STATO_VALIDO', 1320); //VALIDO
define('SINC_FA_STATO_ERRORE', 2013);// ERRORE MATERIALE
define('SINC_FA_STATO_INVALIDATO', 1322);//INVALIDATO PER RICALCOLO
define('SINC_FA_STATO_ERRORE_SISTEMA', 2560);//ERRORE MATERIALE DA SISTEMA
define('CODICE_REGIONE_SICILIA',19);//CODICE REGIONE SICILIA

require_once ROOT . "lib/classes/sincsian/AnagraficaFascicolo.php";
require_once ROOT . "lib/classes/sincsian/AnagraficaMandato.php";
require_once ROOT . "lib/classes/sincsian/AnagraficaSoggetto.php";
require_once ROOT . "lib/classes/sincsian/RappresentanteLegale.php";
require_once ROOT . "lib/classes/sincsian/AnagraficaComuni.php";
require_once ROOT . "lib/classes/sincsian/AnagraficaUffici.php";
require_once ROOT . "lib/classes/sincsian/Isola.php";
