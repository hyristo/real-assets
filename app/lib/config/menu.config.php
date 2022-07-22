<?php

define('SUPER_USER', 1);

define('GRUPPO_AZIENDE', 34);
define('GRUPPO_CAA', 35);
define('GRUPPO_AMMINISTRATORE', 1);
define('GRUPPO_CARD', 2);
define('GRUPPO_BIBLIOTECA', 3);
define("ROLE_CODE_FAKE", ''); // IMPOSTARE IL GRUPPO SOLO PER BYPASSARE IL ROLE_CODE DELLO IAM


$MENUITEMS[] = array(
    "id" => "DASHBOARD",
    "text" => "Dashboard",
    "description" => "La tua pagina iniziale",
    "icon" => "fas fa-tachometer-alt",
    "url" => HTTP_PRIVATE_SECTION . "index.php",    
    "handler" => "",
    "super_user" => 0,
    "group_id" => array()
);

$MENUITEMS[] = array(
    "id" => "CAL",
    "text" => "Scadenziario",
    "description" => "Scadenziario locazioni",
    "icon" => "fas fa-calendar-alt",
    "url" => BASE_HTTP . "modules/calendar/",
    "handler" => ""
);
/*
$MENUITEMS[] = array(
    "id" => "GESTDONAZIONI",
    "text" => "Donazioni",
    "description" => "Gestione delle donazioni",
    "icon" => "fas fa-people-carry",
    "url" => HTTP_PRIVATE_SECTION . "dashboard.php",    
    "handler" => "",
    "super_user" => SUPER_USER,
    "group_id" => array(GRUPPO_AMMINISTRATORE, GRUPPO_CARD),
    "children" => array(
        array(
            "id" => "CARD",
            "text" => "Mamory Card",
            "description" => "Anagrafica memory card",
            "icon" => "far fa-credit-card",
            "url" => HTTP_PRIVATE_SECTION . "memory_card.php",
            "handler" => "",
            "super_user" => SUPER_USER, // visibile solo all'utente SUPER USER
            "group_id" => array(GRUPPO_AMMINISTRATORE, GRUPPO_CARD)
        ),array(
            "id" => "DONAZIONI",
            "text" => "Gestione donazioni",
            "description" => "Gestione delle donazioni con le card",
            "icon" => "fas fa-hand-holding-usd",
            "url" => HTTP_PRIVATE_SECTION . "donazioni.php",
            "handler" => "",
            "super_user" => SUPER_USER, // visibile solo all'utente SUPER USER
            "group_id" => array(GRUPPO_AMMINISTRATORE,GRUPPO_CARD)
        )
    )
);
*/
$MENUITEMS[] = array(
    "id" => "ANAGRAFICHE",
    "text" => "Anagrafiche",
    "description" => "Gestione anagrafiche",
    "icon" => "fas fa-cogs",
    "url" => "#",
    "handler" => "",
    "super_user" => SUPER_USER,
    "group_id" => array(GRUPPO_AMMINISTRATORE, GRUPPO_CARD),
    "children" => array(
        array(
            "id" => "PATRIMONIO",
            "text" => "Patrimonio",
            "description" => "Gestione del patrimonio con i relativi contratti di locazione",
            "icon" => "fas fa-city",
            "url" => HTTP_PRIVATE_SECTION . "patrimonio.php",
            "handler" => "",
            "super_user" => SUPER_USER, // visibile solo all'utente SUPER USER
            "group_id" => array(GRUPPO_AMMINISTRATORE,ANAG_PERSONE)
        ),array(
            "id" => "PERSONE",
            "text" => "Locatari",
            "description" => "Gestione anagrafiche locatari",
            "icon" => "fas fa-id-card-alt",
            "url" => HTTP_PRIVATE_SECTION . "persone.php",
            "handler" => "",
            "super_user" => SUPER_USER, // visibile solo all'utente SUPER USER
            "group_id" => array(GRUPPO_AMMINISTRATORE,ANAG_PERSONE)
        ),array(
            "id" => "CODICI",
            "text" => "Anagrafiche varie",
            "description" => "Anagrafica codici vari",
            "icon" => "fas fa-code",
            "url" => HTTP_PRIVATE_SECTION . "codici_vari.php",
            "handler" => "",
            "super_user" => SUPER_USER, // visibile solo all'utente SUPER USER
            "group_id" => array(GRUPPO_AMMINISTRATORE, GRUPPO_CARD)
        )
    )
);

$MENUITEMS[] = array(
    "id" => "ADMIN",
    "text" => "Amministratore",
    "description" => "Gestione sistema",
    "icon" => "fas fa-cogs",
    "url" => "#",
    "handler" => "",
    "super_user" => SUPER_USER,
    "group_id" => array(GRUPPO_AMMINISTRATORE, GRUPPO_OPERATORI),
    "children" => array(
        /*array(
            "id" => "STATISTICHE",
            "text" => "Statistiche",
            "description" => "Report statistici",
            "icon" => "fas fa-chart-line",
            "url" => HTTP_PRIVATE_SECTION . "analisys.php",
            "handler" => "",
            "super_user" => SUPER_USER, // visibile solo all'utente SUPER USER
            "fase" => 0,
            "group_id" => array(GRUPPO_AMMINISTRATORE, GRUPPO_OPERATORI)
        ),*/
        array(
            "id" => "OPERATORI",
            "text" => "Operatori",
            "description" => "Elenco degli operatori",
            "icon" => "fas fa-users",
            "url" => BASE_HTTP . "modules/operatori/operatori.php",
            "handler" => "",
            "super_user" => SUPER_USER, // visibile solo all'utente SUPER USER
            "fase" => 0,
            "group_id" => array(GRUPPO_AMMINISTRATORE, GRUPPO_OPERATORI)
        ),
        array(
            "id" => "LOGSFE",
            "text" => "Logs",
            "description" => "Log di sistema",
            "icon" => "fas fa-stream",
            "url" => BASE_HTTP . "modules/logs/logs.php",
            "handler" => "",
            "super_user" => SUPER_USER,
            "fase" => 0,
            "group_id" => array(GRUPPO_AMMINISTRATORE, GRUPPO_LOGS)
        )
    )
);


$MENUITEMS[] = array(
    "id" => "LOGOUT",
    "text" => "Esci",
    "description" => "Esci dal sistema",
    "icon" => "fas fa-sign-out-alt",
    "url" => BASE_HTTP . "logout.php",
    "handler" => "",
    "super_user" => 0,
    "group_id" => array()
    
);
