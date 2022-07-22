<?php

/**
 * CONFIFURAZIONI SPID
 */
define('SPID_TIM', 'TI Trust Technologies srl');
define('SPID_INFOCERT', 'InfoCert S.p.A.');
define('SPID_POSTE', 'Poste Italiane SpA');
define('SPID_SIELTE', 'Sielte S.p.A.');
define('SPID_INTESA', 'IN.TE.S.A. S.p.A.');
define('SPID_SPIDITALIA', 'Register.it S.p.A.');
define('SPID_LEPIDA', 'Lepida S.p.A.');
define('SPID_NAMIRIAL', 'Namirial');
define('SPID_ARUBA', 'ArubaPEC S.p.A.');
define('SPID_TEST', 'TEST');
define('SPID_VALIDATOR', 'VALIDATOR');
#############################################

define("RETURNTOSPID", "peo"); // IDENTIFICATIVO APPLICATIVO PER IL RETURN SPID
define("FAKE_SPID", 1); // Enable fake Spid info
define("CF_SPID_TEST", "TINIT-CNSSVT73H13M088G");  // fake Spid cf da appendere TINIT- DLSGCM47D15L331H  NGHMNL80A68H163V CNSSVT73H13M088G - CCHMNL57T03M088S - VTTLFA60P20E017H -13224 - 12032
define("SPID_TEST_BTN", 1); // Enable fake Spid info
define("PERCORSO_CONSENSO", BASE_FOLDER . PRIVATE_SECTION. "consenso.php");

if (DEV) {
    define('DOMINIO_SPID', 'https://sipars.igea-lab.it/spid/login-spid.php?ReturnTo=' . RETURNTOSPID . '&idp=');
} else {
    define('DOMINIO_SPID', 'https://incentivi.regione.sicilia.it/spid/login-spid.php?ReturnTo=' . RETURNTOSPID . '&idp=');
}
?>