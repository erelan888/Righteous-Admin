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
?>
    <html>
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Customer Service Home - RCA Admin</title>
    <meta name="robots" content="noindex,nofollow"/>
    <?php
        include_once("includes/inc-html-header.php");
    ?>
    </head>
    <body>
        <?php 
            $title = "CS Home";    
            include_once("includes/inc-header.php"); 
        ?>
        <div class="container-fluid skip-nav">
            <div class="row">
                <div class="col">
                        <h2>Customer Service</h2>
                        <h3 style="color: #D02030; font-style:italic;">Online Store Reporting</h3>
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
                                    var url = "https://admin.authenticmerch.com/customer-service.php?&date_from=" + fromDate + "&date_to=" + toDate;
                                    window.open(url,'_self',false);
                                });
                            });
                        </script>
                </div>
            </div>
            </div><!-- End Row -->
            <div class="row">
                <div class="col">
                <div class="card" style="width: 100%;">
                    <h6 class="card-header">Quickbooks Upload Files</h6>
                    <div class="card-body">
                    <ul style="list-style-type: none;margin-left: 0px; padding-left: 0px;">
                        <li style="margin-bottom: 10px;"><a data="Purchase Order" class="btn btn-info generateUpload" ><span class="spinner-grow" role="status" style="display: none; font-size: 15px;"></span><i class="fas fa-money-check-alt"></i> Purchase Order Upload</a></li>
                        <li><a data="Credit Card" class="btn btn-info generateUpload"><span class="spinner-grow" role="status" style="display: none; font-size: 15px;"></span><i class="fas fa-credit-card"></i> Credit Card Upload</a></li>
                    </ul>
                    <script type="text/javascript">
                        $(document).ready(function(){
                            $(".generateUpload").click(function(){
                                var toDate   = "<?php echo (isset($date_to)?$date_to:""); ?>";
                                var fromDate = "<?php echo (isset($date_from)?$date_from:""); ?>";
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
                                    else{
                                        alert ("Type blank");
                                    } 
                                });
                            });
                        </script>
                        </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <div class="col">
                        <div class="card" style="width: 100%;">
                            <h6 class="card-header">Inventory Reports</h6>
                            <div class="card-body">
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
                    <div class="col">
                        <div class="card" style="width: 100%;">
                            <h6 class="card-header">Sales Reports</h6>
                            <div class="card-body">
                            </div><!-- end card body -->
                        </div><!-- end card -->
                    </div>
            </div><!-- End Row -->
        </div>
    </body>
</html>
     