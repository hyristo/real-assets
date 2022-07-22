<?php ?>
<script src="<?= BASE_HTTP ?>assets/js/jquery/jquery-3.5.1.min.js" type="text/javascript"></script>
<script src="<?= BASE_HTTP ?>assets/js/jquery/jquery-ui.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" type="text/javascript"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" type="text/javascript"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" type="text/javascript"></script>
<? if (UNICO_JS_CSS) { ?>
    <script src="<?= BASE_HTTP ?>assets/js/SiparsApi.js" type="text/javascript"></script>
<? } ?>


<script type="text/javascript">


<? if (!UNICO_JS_CSS) { ?>
        function require(script) {
            $.ajax({
                url: script,
                dataType: "script",
                async: false, // <-- This is the key
                success: function () {
                    // all good...
                },
                error: function () {
                    throw new Error("Could not load script " + script);
                }
            });
        }
        require("<?= BASE_HTTP ?>assets/dist/js/bootstrap.min.js");
        require("<?= BASE_HTTP ?>js/sweetalert2.min.js");
        require("<?= BASE_HTTP ?>js/utils.js");
        require("<?= BASE_HTTP ?>js/codicefiscale.js");
        require("<?= BASE_HTTP ?>js/jquery.redirect.js");        
        // DATA TABLES
        require("<?= BASE_HTTP ?>assets/js/DataTables/Responsive-2.2.3/js/dataTables.responsive.min.js");
        require("<?= BASE_HTTP ?>assets/js/DataTables/Responsive-2.2.3/js/dataTables.responsive.min.js");
        require("<?= BASE_HTTP ?>assets/js/DataTables/RowGroup-1.1.1/js/dataTables.rowGroup.min.js");        
        require("<?= BASE_HTTP ?>assets/js/DataTables/Buttons-1.6.0/js/dataTables.buttons.min.js");
        require("<?= BASE_HTTP ?>assets/js/DataTables/Buttons-1.6.0/js/buttons.print.min.js");
        require("<?= BASE_HTTP ?>assets/js/DataTables/Buttons-1.6.0/js/buttons.colVis.min.js");
        require("<?= BASE_HTTP ?>assets/js/DataTables/Buttons-1.6.0/js/buttons.html5.min.js");
        require("<?= BASE_HTTP ?>assets/js/DataTables/pdfmake-0.1.36/pdfmake.min.js");
        require("<?= BASE_HTTP ?>assets/js/DataTables/pdfmake-0.1.36/vfs_fonts.js");
        require("<?= BASE_HTTP ?>assets/js/DataTables/JSZip-2.5.0/jszip.min.js");
        require("<?= BASE_HTTP ?>assets/js/DataTables/Select-1.3.1/js/dataTables.select.js");
        require("<?= BASE_HTTP ?>assets/js/jquery/jquery.mask.min.js");
        // DATA TABLES
        // SELECT 2        
        require("<?= BASE_HTTP ?>assets/js/select2-4.0.13/dist/js/select2.full.min.js");
        require("<?= BASE_HTTP ?>assets/js/select2-4.0.13/dist/js/i18n/it.js");        
<? } ?>
    

    $(document).ready(function () {

        $(function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    });
    var ROOT_URL = window.location.protocol + "//" + window.location.host;
    $.extend($.fn.dataTable.defaults, {
        "language": {
            "url": BASE_HTTP + "/assets/js/DataTables/plug-ins/i18n/it_it.json"
        },
        "drawCallback": function (settings) {
            
            $('.tooltipped').tooltip();
        },
        "fnPreDrawCallback":function(dt){            
            $('.dataTables_processing').attr('class', 'customloadingDt');            
        },
        dom: 'lBfrtip',
        "aLengthMenu": [[10, 20, 40, 80, 160, 999999], ["10", "20", "40", "80", "160", "All"]],
        "oClasses": {
            "sLengthSelect": 'selectForPages'
        },
        //"responsive": true,
        responsive: {
            details: {
                display: $.fn.dataTable.Responsive.display.childRowImmediate,
                type: ''
            }
        },
        
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy material-icons dp48 tooltipped" data-position="top" data-tooltip="COPIA"></i>',
                className: 'sipars',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv material-icons dp48 tooltipped" data-position="top" data-tooltip="ESPORTA CSV"></i>',
                className: 'sipars',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel material-icons dp48 tooltipped" data-position="top" data-tooltip="ESPORTA XLS"></i>',
                className: 'sipars',
                exportOptions: {
                    columns: ':visible'
                }
            },
            // {
                // extend: 'pdf',
                // text: '<i class="fas fa-file-pdf material-icons dp48 tooltipped" data-position="top" data-tooltip="ESPORTA PDF"></i>',
                // className: 'sipars',
                // exportOptions: {
                    // columns: ':visible',
                    // modifier: {
                        // page: 'all',
                        // length: 0,
                        // search: 'none'
                    // }
                // }
            // },
            {
                extend: 'print',
                text: '<i class="fas fa-print material-icons dp48 tooltipped" data-position="top" data-tooltip="STAMPA"></i>',
                className: 'sipars',
                exportOptions: {
                    columns: ':visible'
                }
            },
            {
                extend: 'colvis',
                className: 'sipars',
                text: '<i class="fas fa-table material-icons dp48 tooltipped" data-position="top" data-tooltip="COLONNE DA VISUALIZZARE"></i>'
            }/*, {
                text: '<i class="fas fa-sync-alt material-icons dp48 tooltipped" data-position="top" data-tooltip="AGGIORNA I DATI"></i>',
                className: "buttonAgg",
                action: function (e, dt, node, config) {
                    dt.ajax.reload();
                }
            }*/
        ]
    });
</script>

