<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?= APP_NAME ?>">
    <title><?= APP_NAME ?></title>
    <?
    if (UNICO_JS_CSS) {
        ?>
        <link href="<?= BASE_HTTP ?>assets/SiparsApi.css?t=<?= time() ?>" rel="stylesheet" type="text/css" />
    <? } else { ?>

        <link href="<?= BASE_HTTP ?>assets/dist/css/bootstrap.min.css?t=<?= time() ?>" rel="stylesheet" type="text/css" />    
        <link href="<?= BASE_HTTP ?>assets/sweetalert2.min.css?t=<?= time() ?>" rel="stylesheet" type="text/css" />

        <link href="<?= BASE_HTTP ?>assets/js/DataTables/datatables.min.css?t=<?= time() ?>" rel="stylesheet" type="text/css"/>
        <link href="<?= BASE_HTTP ?>assets/js/DataTables/Responsive-2.2.3/css/responsive.dataTables.min.css?t=<?= time() ?>" rel="stylesheet" type="text/css"/>
        <link href="<?= BASE_HTTP ?>assets/js/DataTables/RowGroup-1.1.1/css/rowGroup.dataTables.min.css?t=<?= time() ?>" rel="stylesheet" type="text/css"/>
        <link href="<?= BASE_HTTP ?>assets/js/DataTables/Buttons-1.6.0/css/buttons.dataTables.min.css?t=<?= time() ?>" rel="stylesheet" type="text/css"/>
        <link href="<?= BASE_HTTP ?>js/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?= BASE_HTTP ?>assets/loader.css?t=<?= time() ?>" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_HTTP ?>assets/js/select2-4.0.13/dist/css/select2.min.css?t=<?= time() ?>" rel="stylesheet" type="text/css" />
        <link href="<?= BASE_HTTP ?>assets/custom.css?t=<?= time() ?>" rel="stylesheet" type="text/css" />
    <? } ?>
    <link href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <!-- BEGIN: CSS Maps-->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">
    <!-- END: CSS Maps-->
    <!-- BEGIN: CSS Fonts Icons-->
    <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css' rel="stylesheet" type="text/css" />    

    <script type="text/javascript">
        var WS_CALL = "<?= WS_CALL ?>";
        var HTTP = "<?= PREFIX_HTTP ?>";
        var BASE_HTTP = "<?= BASE_HTTP ?>";
        var PRIVATE_SECTION = "<?= PRIVATE_SECTION ?>";
        var HTTP_PRIVATE_SECTION = "<?= HTTP_PRIVATE_SECTION ?>";
        var CONSENSO = "<?= $LoggedAccount->AUTORIZZAZIONE_TRATTAMENTO ?>";
        var RELPREPARADOMANDA = "<?= RELPREPARADOMANDA ?>";
        var RELDCLICKDAY = "<?= RELDCLICKDAY ?>";
        var PERCENTUALE = "<?= PERCENTUALE ?>";
        var MESI = "<?= MESI ?>";
        var MESI_RIFERIMENTO = "<?= MESI_RIFERIMENTO ?>";
        var DOMINIO_SPID = "<?= DOMINIO_SPID ?>";
        var SPID_TIM = "<?= SPID_TIM ?>";
        var SPID_INFOCERT = "<?= SPID_INFOCERT ?>";
        var SPID_POSTE = "<?= SPID_POSTE ?>";
        var SPID_SIELTE = "<?= SPID_SIELTE ?>";
        var SPID_INTESA = "<?= SPID_INTESA ?>";
        var SPID_SPIDITALIA = "<?= SPID_SPIDITALIA ?>";
        var SPID_LEPIDA = "<?= SPID_LEPIDA ?>";
        var SPID_NAMIRIAL = "<?= SPID_NAMIRIAL ?>";
        var SPID_ARUBA = "<?= SPID_ARUBA ?>";
        var SPID_TEST = "<?= SPID_TEST ?>";
        var SPID_VALIDATOR = "<?= SPID_VALIDATOR ?>";
        var MAX_SIZE_FILE = "<?= MAX_SIZE_FILE ?>";
        var MAX_SIZE_FILE_P7M = "<?= MAX_SIZE_FILE_P7M ?>";
        var MAX_SIZE_FILE_UPLOAD = "<?= MAX_SIZE_FILE_UPLOAD ?>";
        var ROOT_UPLOAD_FOTO = "<?= ROOT_UPLOAD_FOTO ?>";
        var ROOT_UPLOAD_PHOTO = "<?= ROOT_UPLOAD_PHOTO ?>";
        var UPLOAD_FOTO = "<?= UPLOAD_FOTO ?>";
        //        var DOCUMENTI_FASE_1 = "<?= DOCUMENTI_FASE_1 ?>";
        //        var DOCUMENTI_FASE_2 = "<?= DOCUMENTI_FASE_2 ?>";
//        var DOCUMENTI_FASE_3 = "<?= DOCUMENTI_FASE_3 ?>";

        
        var ID_ACCOUNT = "<?= $LoggedAccount->ID ?>";
        var CODICE_FISCALE = "<?= $LoggedAccount->CODICE_FISCALE ?>";
        var DIFESA = "<?= DIFESA ?>";
        var NUTRIZIONE = "<?= NUTRIZIONE ?>";
        var IRRIGAZIONE = "<?= IRRIGAZIONE ?>";
        var OPERAZIONE = "<?= OPERAZIONE ?>";
        var RACCOLTA = "<?= RACCOLTA ?>";
        var CLASSE_FITOSANITARI = "<?= CLASSE_FITOSANITARI ?>";
        var CLASSE_FERTILIZZANTI = "<?= CLASSE_FERTILIZZANTI ?>";

    </script>

</head>

