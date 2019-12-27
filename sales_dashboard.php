<?php
 ob_start();
 session_start();
 
 $user_id = $_SESSION["user_id"];
 $username = $_SESSION['username'];
 $permission_level = $_SESSION["permission_level"];
 if(!$user_id){
     //redirect to login
     header("Location: https://admin.authenticmerch.com");
 }
 else{
     //add_activity($user_id,"Added User");
 }
 error_reporting( E_ALL );
 ini_set( "display_errors", 1 );

  //DB Server info
  $servername = "localhost";
  $db_username = "fe32045_dev_dustin";
  $db_password = "@TbGG3Fdau1m";
  $db = "fe32045_admin_catalog";
  // Create connection
  global $conn;
  $conn = new mysqli($servername, $db_username, $db_password,$db);
  
  // Check connection
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  } 
if(isset($_GET['client_id'])){
    $sales_query = "SELECT * FROM `admin_client_details` WHERE `_client_id`=" . $_GET['client_id'];
    $sales_results = mysqli_query($conn, $sales_query);
    $results = mysqli_fetch_assoc($sales_results);
    
    $client_name = $results['client_name'];
    $client_id   = $results['_client_id'];

    $date_from = null;
    $date_to   = null;

    if(isset($_GET['date_from'])){
        $date_from = $_GET['date_from'];
        $date_to   = $_GET['date_to'];
    }
?>
<!doctype html>
<html lang='en'>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title> <?php echo $client_name; ?> Sales Dashboard - RCHQ Admin Area</title>
        <meta name="robots" content="noindex,nofollow"/>
        <?php
            include_once("includes/inc-html-header.php");
        ?>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
    </head>
    <body>
        <?php 
            $title="Sales Dashboard";    
            include_once("includes/inc-header.php"); 
        ?>
        <div class="container-fluid skip-nav" style="clear: both;">
            <?php
            if($date_from == null && $date_to == null){
                $sales_query = "SELECT YEAR(date_created) as `year`,
                MONTHNAME(date_created) as `month`,
                 SUM(total) as sales, AVG(total) as average 
                 FROM `admin_client_woocommerce_orders` WHERE `client_id`=" . $client_id 
                 . " GROUP BY YEAR(date_created), MONTH(date_created)";
                $sales_results = mysqli_query($conn, $sales_query);

                $timestamp_raw = "";
                $year_raw = "";
                $sales_raw = "";
                $averages_raw = "";
                $total_sales = 0;
                while($sale = mysqli_fetch_assoc($sales_results)){
                    $timestamp_raw .= $sale['month'] . ",";
                    $year_raw      .= $sale['year']  . ",";
                    $sales_raw     .= $sale['sales'] . ",";
                    $averages_raw  .= number_format($sale['average'],2) . ",";
                    $total_sales   += $sale['sales'];
                }
            }
            else{
                $sales_query = "SELECT YEAR(date_created) as `year`,
                MONTHNAME(date_created) as `month`,
                 SUM(total) as sales, AVG(total) as average   
                 FROM `admin_client_woocommerce_orders` WHERE `client_id`=" . $client_id 
                 . " AND date_created BETWEEN '" . $date_from . "' AND '" . $date_to
                 . "' GROUP BY YEAR(date_created), MONTH(date_created)";
                $sales_results = mysqli_query($conn, $sales_query);

                $timestamp_raw = "";
                $year_raw = "";
                $sales_raw = "";
                $averages_raw = "";
                $total_sales = 0;
                while($sale = mysqli_fetch_assoc($sales_results)){
                    $timestamp_raw .= $sale['month'] . ",";
                    $year_raw      .= $sale['year']  . ",";
                    $sales_raw     .= $sale['sales'] . ",";
                    $averages_raw  .= number_format($sale['average'],2) . ",";
                    $total_sales   += $sale['sales'];
                }
            }
            ?>
             <div class="row">
                 <div class="col">
                    <h2><?php echo $client_name; ?></h2>
                    <h3 style="color: #D02030; font-style:italic;">Sales Dashboard</h3>
                </div>
                <div class="col">
                 <div id="date_block">
                  <p style="font-size: 12px;margin-bottom: 0px;">Choose Date Range:</p>
                            <input type="date" name="date_from" id="date_from" value="<?php echo $date_from; ?>"  class="form-control-sm"/> to <input type="date" name="date_to" id="date_to" value="<?php echo $date_to; ?>" class="form-control-sm"/>
                            <a class="btn btn-info btn-sm" style="cursor: pointer; position: relative; top: -2px;" id="refresh_date"><i class="fas fa-undo" style="color: #fff;"></i></a>
                            <script type="text/javascript">
                                $(document).ready(function(){
                                    $("#refresh_date").click(function(){
                                        var fromDate = $("#date_from").val();
                                        var toDate   = $("#date_to").val();
                                        var clientId = <?php echo $client_id; ?>;
                                        var url = "https://admin.authenticmerch.com/sales_dashboard.php?client_id=" + clientId
                                                + "&date_from=" + fromDate + "&date_to=" + toDate;
                                        window.open(url,'_self',false);
                                    });
                                });
                            </script>
                            <h4 style="color: #D02030;margin-top: 10px;"><span style="color: #212529;">Total Sales:</span><?php echo "$ " . number_format($total_sales); ?></h4>
                    </div>
                    <script type="text/javascript">
                        $(document).ready(function(){
                            $("#generate_full_product_sales_report_button").click(function(){
                                var date = "<?php echo $date_to; ?>";
                                var urlData = "generate_sales_report.php?client_id=<?php echo $client_id; ?>&date_to=<?php echo $date_to; ?>&date_from=<?php echo $date_from; ?>";
                                if(date != ""){
                                    $.ajax({
                                    url: urlData,
                                    success: function (data) {
                                        var csvData  = 'data:application/csv;charset=utf-8,' + encodeURIComponent(data);
                                        var fileName = "<?php echo $client_name?>-Sales-Report-<?php echo date('m-d-Y');?>.csv";

                                        if (window.navigator.msSaveBlob) { // IE 10+
                                            alert('IE' + csv);
                                            window.navigator.msSaveOrOpenBlob(new Blob([data], {type: "text/plain;charset=utf-8;"}), fileName)
                                        } 
                                        else {
                                            
                                            var downloadLink = document.createElement("a");
                                            var blob = new Blob([data], {type: "text/plain;charset=utf-8;"});
                                            var url = URL.createObjectURL(blob);
                                            downloadLink.href = url;
                                            downloadLink.download = fileName;

                                            document.body.appendChild(downloadLink);
                                            downloadLink.click();
                                            document.body.removeChild(downloadLink);
                                            
                                        }
                                    },
                                    error: function (xhr, status, error) {
                                        if (xhr.status > 0){
                                            alert(status); // status 0 - when load is interrupted
                                        } 
                                    }
                                });
                            }                                                    
                            });
                            $("#export_data_button").click(function(){
                                //export that data homie!
                                var titleString = "Year,Month,Sales";
                                var EOL = "\r\n";
                                var delimiter = ",";

                                var output = titleString + EOL;
                                var _count = 1;
                                $("#sales-data-table td").each(function(){
                                    if((_count % 2) != 0){
                                        output += $(this).text().replace(" ",",");
                                        output += delimiter;
                                    }
                                    else{
                                        output += $(this).text().replace(",","");
                                        output += EOL;
                                    }
                                    _count++;
                                });

                                var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(output);

                                if (window.navigator.msSaveBlob) { // IE 10+
                                    alert('IE' + csv);
                                    window.navigator.msSaveOrOpenBlob(new Blob([output], {type: "text/plain;charset=utf-8;"}), "data.csv")
                                } 
                                else {
                                    
                                    var downloadLink = document.createElement("a");
                                    var blob = new Blob([output], {type: "text/plain;charset=utf-8;"});
                                    var url = URL.createObjectURL(blob);
                                    downloadLink.href = url;
                                    downloadLink.download = "data.csv";

                                    document.body.appendChild(downloadLink);
                                    downloadLink.click();
                                    document.body.removeChild(downloadLink);
                                    
                                   // $(this).attr({ 'download': "csvname.csv", 'href': output, 'target': '_blank' }); 
                                }
                            });
                            $("#export_product_data_button").click(function(){
                                var EOL = "\r\n";
                                var delimiter = ",";
                                var output = "";

                                $("#best-selling-products th").each(function(){
                                    output += $(this).text() + delimiter;
                                });
                                output += EOL;

                                _count = 1;
                                $("#best-selling-products td").each(function(){
                                    
                                    if((_count % 3) == 0){
                                        output += $(this).text().replace(",","") + EOL;
                                    }
                                    else{
                                        output += $(this).text().replace(",","") + delimiter;
                                    }
                                    _count++;
                                });

                                var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(output);

                                if (window.navigator.msSaveBlob) { // IE 10+
                                    //alert('IE' + csv);
                                    window.navigator.msSaveOrOpenBlob(new Blob([output], {type: "text/plain;charset=utf-8;"}), "best-sellers.csv")
                                } 
                                else {
                                    var downloadLink = document.createElement("a");
                                    var blob = new Blob([output], {type: "text/plain;charset=utf-8;"});
                                    var url = URL.createObjectURL(blob);
                                    downloadLink.href = url;
                                    downloadLink.download = "best-sellers.csv";

                                    document.body.appendChild(downloadLink);
                                    downloadLink.click();
                                    document.body.removeChild(downloadLink);
                                }
                            });
                        });
                    </script>
                </div>
            </div>
            
                <a href="https://admin.authenticmerch.com/dashboard.php" class="btn"> <i class="fas fa-undo"></i> Back To Dashboard</a>
                <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="float: right;">
                    <i class="fas fa-file-download" style="padding-right: 10px; padding-left: 10px;"></i> Reports
                </a>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink" >
                
                    <a style="cursor: pointer;" id="export_data_button" class="dropdown-item"><i class="fas fa-file-download"></i> Export Sales Data</a>
                    <a style="cursor: pointer;" id="export_inventory_button" class="dropdown-item"><i class="fas fa-file-download"></i> Export Inventory Snapshot</a>
                    <a style="cursor: <?php echo (empty($date_to)?"disabled":"pointer") ?>;" id="generate_full_product_sales_report_button" title="Add date filters to enable this report"  class="dropdown-item"><i class="fas fa-file-download"></i> Export Product Sales Report</a>
                    <?php
                        if($username == "dustingunter" || $username == "Jennifer" || $username = "Jerica" || $username == "Amy" || $username = "Mary"){
                    //     if($username == "dustingunter"){
                    ?>
                    <a data="Purchase Order" class="generateUpload dropdown-item" style="cursor: pointer;"><span class="spinner-grow" role="status" style="display: none; font-size: 15px;"></span><i class="fas fa-money-check-alt"></i> Purchase Order Upload</a>
                    <a data="Credit Card" class="generateUpload dropdown-item"  style="cursor: pointer;"><span class="spinner-grow" role="status" style="display: none; font-size: 15px;"></span><i class="fas fa-credit-card"></i> Credit Card Upload</a>
                    <?php
                        } 
                    ?>
                </div>
            
            <script type="text/javascript">
                $(document).ready(function(){
                    $(".generateUpload").click(function(){
                         var toDate   = "<?php echo $date_to; ?>";
                         var fromDate = "<?php echo $date_from; ?>";
                         var type     = $(this).attr('data');
                         var spinner  = $(this).children(".spinner-grow");
                         $(this).children(".spinner-grow").fadeIn();
                         var urlData = "test/generate_upload_order_lines.php?type=" + type;
                                if(type != ""){
                                    $.ajax({
                                    url: urlData,
                                    success: function (data) {
                                        var csvData  = 'data:application/csv;charset=utf-8,' + encodeURIComponent(data);
                                        var fileName = type + "-upload-report-<?php echo date('m-d-Y');?>.csv";

                                        if (window.navigator.msSaveBlob) { // IE 10+
                                            alert('IE' + csv);
                                            window.navigator.msSaveOrOpenBlob(new Blob([data], {type: "text/plain;charset=utf-8;"}), fileName)
                                        } 
                                        else {
                                            
                                            var downloadLink = document.createElement("a");
                                            var blob = new Blob([data], {type: "text/plain;charset=utf-8;"});
                                            var url = URL.createObjectURL(blob);
                                            downloadLink.href = url;
                                            downloadLink.download = fileName;

                                            document.body.appendChild(downloadLink);
                                            downloadLink.click();
                                            document.body.removeChild(downloadLink);
                                            
                                        }
                                        $(spinner).fadeOut();
                                    },
                                    error: function (xhr, status, error) {
                                        if (xhr.status > 0){
                                            alert(status); // status 0 - when load is interrupted
                                        } 
                                    },
                                    timeout: 30000
                                });
                            } 
                    });
                    $("#export_inventory_button").click(function(){
                        var urlData = "generate_inventory_snapshot.php?client_id=" + <?php echo $client_id ?>;
                        $.ajax({
                            url: urlData,
                            success: function (data) {
                                var csvData  = 'data:application/csv;charset=utf-8,' + encodeURIComponent(data);
                                var fileName = <?php echo $client_id ?> + "-client-inventory-snapshot-<?php echo date('m-d-Y');?>.csv";

                                if (window.navigator.msSaveBlob) { // IE 10+
                                    alert('IE' + csv);
                                    window.navigator.msSaveOrOpenBlob(new Blob([data], {type: "text/plain;charset=utf-8;"}), fileName)
                                } 
                                else {
                                    
                                    var downloadLink = document.createElement("a");
                                    var blob = new Blob([data], {type: "text/plain;charset=utf-8;"});
                                    var url = URL.createObjectURL(blob);
                                    downloadLink.href = url;
                                    downloadLink.download = fileName;

                                    document.body.appendChild(downloadLink);
                                    downloadLink.click();
                                    document.body.removeChild(downloadLink);
                                    
                                }
                            },
                            error: function (xhr, status, error) {
                                if (xhr.status > 0){
                                    alert(status); // status 0 - when load is interrupted
                                } 
                            },
                            timeout: 30000
                        });  
                    });
                });
            </script>
            </p>
            <hr>
           <div class="row">
                <div class="col">
                <canvas id="salesDashboard" width="1000" height="450" responsive="false">
                    <!-- Script for graph  -->
                    <script type="text/javascript">
                        var ctx = document.getElementById('salesDashboard').getContext('2d');
                        var timestampsRaw = "<?php echo $timestamp_raw; ?>";
                        var violationsRaw = "<?php echo $sales_raw; ?>";
                        var averagesRaw   = "<?php echo $averages_raw; ?>";
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
                                },{
                                    label: 'Average Order Value',
                                    data: averagesRaw.split(","),
                                    backgroundColor: [
                                        'rgba(19, 132, 150, 0.2)'
                                    ],
                                    borderColor: [
                                        'rgba(19, 132, 150, 1)'
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
                    </script>
                </div>
            </div><!-- END ROW -->
            <div style="clear: both; padding: 15px 0px;"></div>
            <div class="row">
                <div class="col table-breakdown">
                    <h4>By Month Breakdown</h4>
                    <table class="table table-bordered" id="sales-data-table">
                    <thead class="thead-dark">
                            <tr>
                                <th>Date</th>
                                <th>Sales</th>
                                <!-- <th>Average Order</th> -->
                            </tr>
                    </thead>
                    <?php
                        $times_array   = explode(",",$timestamp_raw);
                        $year_array    = explode(",",$year_raw);
                        $sale_array    = explode(",",$sales_raw);
                        //$average_array = explode(",",$averages_raw);

                        $limit = count($sale_array);
                        $count = 2;
                        while($count <= $limit){
                            if(isset($sale_array[count($sale_array)- $count])){
                            ?>
                                <tr>
                                    <td><?php echo $year_array[count($year_array)- $count] . " " . $times_array[count($times_array)- $count]; ?></td>
                                    <td><?php echo "$ " . number_format($sale_array[count($sale_array)- $count],2) ; ?></td>
                                    <!-- <td><?php echo "$ " . number_format($average_array[count($average_array) - $count],2);?></td> -->
                                </tr>
                            <?php
                            }
                            $count++;
                        }
                    ?>
                    </table>
                </div>
                <div class="col table-breakdown"  >
                <h4>Best Selling Products</h4>
                    <table class="table table-bordered" id="best-selling-products">
                    <thead class="thead-dark">
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Sales</th>
                        </tr>
                    </thead>
                    <?php
                        $order_number_query = "";
                        if(!empty($date_from) && !empty($date_to)){
                            $order_number_query = "SELECT number FROM `admin_client_woocommerce_orders` WHERE `client_id`=" . $client_id . " AND date_created BETWEEN '" . $date_from . "' AND '" . $date_to . "';";
                        }
                        else{
                            $order_number_query = "SELECT number FROM `admin_client_woocommerce_orders` WHERE `client_id`=" . $client_id;
                        }
                        
                        $order_number_results = mysqli_query($conn, $order_number_query);
                        $order_list = "";
                        while($order = mysqli_fetch_assoc($order_number_results)){
                                $order_list .= "'" . $order['number'] . "',";
                            }
                        $product_sales_query = "SELECT name, SUM(woo_quantity) as quantity, SUM(subtotal) AS sales 
                            FROM `admin_client_order_line_items`  
                            WHERE `order_id` IN(" . substr_replace($order_list ,"",-1) . ") 
                            GROUP BY name ORDER BY sales DESC LIMIT 10";
                        //$product_query = "SELECT * FROM `admin_client_woocommerce_legacy_products` WHERE `client_id`=" 
                        //. $client_id . " ORDER BY `total_sales` DESC LIMIT 11";
                        $product_results = mysqli_query($conn, $product_sales_query);
                        
                        while($product = mysqli_fetch_assoc($product_results)){
                            ?>
                            <tr>
                               <td><?php echo $product['name'] ?></td> 
                               <td><?php echo $product['quantity'] ?></td> 
                               <td style="white-space: nowrap;"><?php echo "$ " . number_format($product['sales']) ?></td>
                            </tr>
                            <?php
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>

<?php
}
?>