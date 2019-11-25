<?php
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
    session_start();
    include("includes/dbc_con_include.php");

    function get_graph_data($graph_name, $data_values, $data_time_relations){

       $output= "<script type=\"text/javascript\">
                        var ctx = document.getElementById('" . $graph_name . "').getContext('2d');
                        var timestampsRaw = " .  $data_time_relations . ";
                        var violationsRaw = " . $data_values . ";
                        var tooltip = this;
                        var myChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: timestampsRaw.split(","),
                                datasets: [{
                                    label: 'Sales',
                                    data: violationsRaw.split(","),
                                    backgroundColor: [
                                        'rgba(255, 99, 132, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(255, 99, 132, 1)'
                                    ],
                                    borderWidth: 2
                                }],
                            options: {
                                responsive: false,
                                scales: {
                                    yAxes: [{
                                        ticks: {
                                            beginAtZero: true
                                                }
                                            }]
                                        }
                                    }
                                }
                            });
                    </script>";
        return $output;
    }
?>
<!Doctype html>
<html lang='en'>
    <head>
        <title>Dashboard | AmzPowerhouse.com</title>
        <?php include_once("includes/html_head_include.php"); ?>
    </head>
    <body>
        <?php  include("includes/header_include.php"); ?>
        
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                   <div class="spinner-grow" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="col">
                    <div class="spinner-grow" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="col">
                    <div class="spinner-grow" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="col">
                    <div class="spinner-grow" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col" id="salesGraph">
                <canvas id="salesDashboardGraph" width="800" height="450" responsive="false">
                    <?php
                        $sales_query = "SELECT date_created, SUM(total) as sales FROM `THIS TABLE NAME` GROUP BY date_created";
                        $sales_results = mysqli_query($conn, $sales_query);
                        $timestamp_raw = "";
                        $sales_raw = "";
                        $total_sales = 0;
                        while($sale = mysqli_fetch_assoc($sales_results)){
                            $timestamp_raw .= $sale['date_created'] . ",";
                            $sales_raw     .= $sale['sales'] . ",";
                        }

                        echo get_graph_data("salesDashboardGraph",$sales_raw,$timestamp_raw);
                    ?>
                </div>
                <div class="col" id="best-seller-graph">
                    <div class="spinner-grow" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="spinner-grow" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="col">
                    <div class="spinner-grow" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="col">
                    <div class="spinner-grow" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="spinner-grow" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="col">
                    <div class="spinner-grow" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="col">
                    <div class="spinner-grow" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <div class="col">
                    <div class="spinner-grow" role="status">
                      <span class="sr-only">Loading...</span>
                    </div>
                </div>
            </div>
            <?php include("includes/footer_include.php"); ?>
        </div>
    </body>
</html>