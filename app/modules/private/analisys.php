<!doctype html>
<?
include '../../lib/api.php';

define("THIS_PERMISSION", array('CODICI_VARI', 'ANAG_PERSONA'));

include_once ROOT . 'layout/include_permission.php';
?>

<html lang="en">
    <? include_once ROOT . 'layout/head.php'; ?>
    <body>
        <?
        include_once ROOT . 'layout/header.php';
        ?>
        <main role="main">
            <header class="masthead masthead-page">                
                <?
                include_once ROOT . 'layout/header_svg.php';
                ?>
            </header>
            <div class="container-fluid">
                <div class="row flex-xl-nowrap">
                    <div class="<?= $colCssContainer ?> bd-content">
                        <?
                        $donazioni = new Donazioni();           
                        //$stats = $donazioni->GetStaticsPro();
                        $dd = $donazioni->MaxData();                        
                        $ultimo_aggiornamento = Date::FormatDate($dd['AGGIORNAMENTO']);
                        //Utils::print_array($stats);
                        ?>
                        <div class="row">
                            <div class="col-sm-6 text-center">                            
                                    <canvas id="divpie" ></canvas>
                            </div>    
                            <div class="col-sm-6 text-center">                            
                                    <canvas id="divbar" ></canvas>
                            </div>    
                        </div>
                        
                    </div>                    
                </div>
            </div>
        </main>
        <? include_once ROOT . 'layout/footer.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function () {
                var myChart2,config;
                
                loadStats();
            });

            function loadStats(reload) {
                //return;

                $('#loader').show();
                $.ajax({
                    method: "POST",
                    url: WS_CALL + '?module=statistiche&action=loadStatistiche',
                    data: {},
                    success: function (result) {
                        $('#loader').hide();
                        var res = eval(result);
                        
                        const data_pie = {
                            labels: res[0].pie.datasets.labels,
                            datasets: [
                                {
                                label: 'Donazioni totali',
                                data: res[0].pie.datasets,
                                backgroundColor: res[0].pie.datasets.backgroundColor,
                                }
                            ]
                        };

                        const config_pie = {
                            type: 'pie',
                            data: data_pie,
                            options: {
                                responsive: true,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                    },
                                    title: {
                                        display: true,
                                        text: 'Dati totali aggiornati al <?=$ultimo_aggiornamento?>'
                                    }
                                },
                                animation:{
                                    onComplete: () => {
                                        delayed = true;
                                    },
                                    animateRotate : true,
                                }
                            },
                            
                        };

                        const data_bar = {
                            labels: res[0].bar.labels,
                            datasets: res[0].bar.datasets
                                                      
                        };
                        const config_bar = {
                            type: 'bar',
                            data: data_bar,
                            options: {
                                responsive: true,
                                plugins: {
                                legend: {
                                    position: 'top',
                                },
                                title: {
                                    display: true,
                                    text: 'Dati mensili aggiornati al <?=$ultimo_aggiornamento?>'
                                }
                                }
                            },
                        };


                        if(!reload){
                            var ctx_pie = document.getElementById('divpie').getContext('2d');                        
                            myChartPie = new Chart(ctx_pie, config_pie);
                            var ctx_bar = document.getElementById('divbar').getContext('2d');                        
                            myChartBar = new Chart(ctx_bar, config_bar);
                        }else{                            
                            myChartPie.update();
                            myChartBar.update();
                        }



                    }

                });

                
                
            }


            

        </script>
    </body>
</html>