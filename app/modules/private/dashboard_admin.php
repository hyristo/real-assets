<!doctype html>
<?
include '../../lib/api.php';

define("THIS_PERMISSION", array('CODICI_VARI', 'ANAG_PERSONA'));


//Utils::print_array($LoggedAccount);exit();

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
                        <?/*$mainMenu->RenderStaticDashboard('DashAdmin')*/?>
                        <?
                        include "statistiche_fake.php";
                        ?>
                    </div>                    
                </div>
            </div>
        </main>
        <? include_once ROOT . 'layout/footer.php'; ?>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script type="text/javascript">

            const ctx = document.getElementById('div1');
            const data = {
                labels: [
                    'Boschi',
                    'Immobili',
                    'Appartemanti',
                    'Magazzino',
                    'Terreni agricoli'
                ],
                datasets: [{
                    label: 'Tipologa del patrimonio',
                    data: [11, 16, 7, 3, 14],
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(75, 192, 192)',
                        'rgb(255, 205, 86)',
                        'rgb(201, 203, 207)',
                        'rgb(54, 162, 235)'
                    ]
                }]
            };
            const config = {
                type: 'polarArea',
                data: data,
                options: {}
            };
            const myChart = new Chart(ctx, config);

            const ctx2 = document.getElementById('div2');
            const data2 = {
                labels: ['Scadute', 'In scadenza', 'Pagate'],
                datasets: [{
                    label: '# of Votes',
                    data: [5, 19, 45],
                    backgroundColor: [
                        'rgb(255,0,0)',
                        'rgb(255,179,0)',
                        'rgb(30,117,0)'
                    ],
                    borderColor: [
                        'rgb(255,0,0)',
                        'rgb(255,179,0)',
                        'rgb(30,117,0)'

                    ],
                    borderWidth: 1
                }]
            };
            const config2 = {
                type: 'pie',
                data: data2,
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            };
            const myChart2 = new Chart(ctx2, config2);

        </script>
    </body>
</html>

