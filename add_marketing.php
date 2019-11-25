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

     function get_product_json($products,$sale_prices){
        $json = array();
        for($i = 0; $i< count($products); $i++){
            $data = array();
            $data['product name'] = $products[$i];
            $data['price'] = $sale_prices[$i];
            $json[] = $data;
        }
        return json_encode($json);
     }
     function get_headline_json($dates, $headlines){
        $json = array();
        for($i = 0; $i< count($dates); $i++){
            $data = array();
            $data['date'] = $dates[$i];
            $data['headline'] = $headlines[$i];
            $json[] = $data;
        }
        return json_encode($json);
     }
     function email_data($output){
         //TODO
         $headers = array("From: emarketing@authenticmerch.com",
            "Reply-To: no-reply@authenticmerch.com",
            "Content-type:text/html;charset=UTF-8",
            "CC: customerservice@rchq.com",
            "X-Mailer: PHP/" . PHP_VERSION );

        $headers = implode("\r\n", $headers);

        mail($email,$email_subject,$output,$headers);
     }
     if(isset($_POST['client_id'])){
        $client_id          = $_POST['client_id'];
        $project_name       = mysqli_real_escape_string($conn,$_POST['project_name']);
        $rep                = $_POST['rep'];
        $splash_page        = $_POST['splash_image_used'];
        $product_page_stock = $_POST['product_page'];
        $product_data       = mysqli_real_escape_string($conn, get_product_json($_POST['product'], $_POST['sale_price']));
        $additional_notes   = mysqli_real_escape_string($conn, $_POST['additional_notes']);
        $timeline_data      = mysqli_real_escape_string($conn, get_headline_json($_POST['email_date'], $_POST['headline']));
        $close_date         = $_POST['close_date'];
        $process_by_date    = $_POST['processing_date'];
        $in_hands_date      = $_POST['in_hands_date'];
        $ship_by_date       = $_POST['ship_by_date'];
        $ship_from          = $_POST['ship_from'];

        $insert_query = "INSERT INTO `admin_client_emarketing_projects` (client_id, `Project Name`, rep, splash_page_bool, product_page_stock, 
        product_data, additional_notes, timeline_data, close_date, ship_from, process_by_date, in_hands_date, ship_by_date) VALUES ('" 
        . $client_id . "','"
        . $project_name . "','"
        . $rep . "','"
        . $splash_page . "','"
        . $product_page_stock . "','"
        . $product_data . "','"
        . $additional_notes . "','"
        . $timeline_data  . "','"
        . $close_date . "','"
        . $ship_from . "','"
        . ($process_by_date ==''? '0000-00-00':$process_by_date) . "','"
        . ($in_hands_date ==''? '0000-00-00':$in_hands_date) . "','"
        . ($ship_by_date ==''? '0000-00-00':$ship_by_date) . "');";

        if($conn->query($insert_query) === TRUE){
            $output = "<center>New E-marketing Plan created by " . $rep . "<br>" .
                        "View it <a href='https://admin.authenticmerch.com/view_marketing.php?client_id=" . $client_id . "\'>here</a><br></center>"
            email_data($output);
            header("Location: https://admin.authenticmerch.com/dashboard.php");

        }
        else{
            die(mysqli_error($conn));
        }
    }
?>
<html>
    <head>
    <title>Add E-Marketing Development Sheet - RCHQ Admin Area</title>
    <meta name="robots" content="noindex,nofollow"/>
        <?php
            include_once("includes/inc-html-header.php");
        ?>
    </head>
    <body>
    <?php 
        include_once("includes/inc-header.php"); 
        if(isset($_GET['client_id'])){
            $client_id = $_GET['client_id'];
            $client_query = "SELECT * FROM `admin_client_details` WHERE _client_id=" . $_GET['client_id'];
            $client_results = mysqli_query($conn,$client_query);

            $client = mysqli_fetch_assoc($client_results);
        }
        else{
            header("Location: https://admin.authenticmerch.com/dashboard.php"); 
        }
    ?>
    <div class="container skip-nav">
        <h1 style="padding-bottom: 20px;"><?php echo $client["client_name"]; ?></h1>
        <h3 style="color: #D02030; font-style:italic;">Create New E-Marketing Sheet</h3>
        <hr/>
        <form name="add_marketing" method="POST" action="">
            <input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
            <input type="hidden" name="rep" value="<?php echo $username; ?>" />
            <ul class="edit_details">
                <li>Project Name*:<input type="text" name="project_name" value="" class="form-control" /></li>
                <li>Include Splash Image?: <select required="required" class="form-control" name="splash_image_used">
                    <option name="splash_yes" value='1'>Yes</option>
                    <option name="splash_no" value='0'>No</option></select></li>
                <li>Product Page: <select required="required" class="form-control" name="product_page">
                    <option name="pre-order-only" value='1'>Pre-order Online</option>
                    <option name="current-item" value='2'>Current Item</option>
                    <option name="pre-order-plus-stock" value='3'>Pre-order Plus Stock</option></select></li>
            </ul>
            <br>
            <h5>Add Products:</h5>
            <hr/>
            <ul class="edit_details" id="emarketing_products">
                <li class="product_sale">Choose Product<select class="form-control" name="product[]" required="required">
                    <option name="" value="">...</option>
                    <?php
                        $product_query = "SELECT * FROM `admin_client_products` WHERE client_id=" . $client_id;
                        $product_results = mysqli_query($conn, $product_query);
                        if(!empty($product_results)){
                            while($product = mysqli_fetch_assoc($product_results)){
                                echo   "<option name=\"" . $product['product_name'] . "\" value=\"" . $product['product_name'] . "\">" . $product['product_name'] . "</option>"; 
                            }
                        }
                        else{
                            die(mysqli_error($conn));
                        }
                        
                    ?>
                </select>
                Price:
                <input type="number"  step="any" name="sale_price[]" class="form-control" required="required" /></li>
            </ul>
            <a style="cursor: pointer;" id="addNewProduct" class="btn"><i class="fas fa-plus-circle"></i> Add Product</a>
            <script type="text/javascript">
                $(document).ready(function(){
                    var productLine = $(".product_sale").html();
                    $("#addNewProduct").click(function(){
                        $("#emarketing_products").append("<hr>").append(productLine);
                    });
                });
            </script>
            <hr/>
            <br>
            <h5>Add Timelines:</h5>
            <hr/>
            <ul class="edit_details" id="emarketing_headlines">
                <li class="email_headline">Choose Date:<input type="date" class="form-control" required="required" name="email_date[]" />
                Headline:
                <input type="text" name="headline[]" class="form-control" required="required" /></li>
            </ul>
            <a style="cursor: pointer;" id="addNewHeadline" class="btn"><i class="fas fa-plus-circle"></i> Add Headline</a>
            <script type="text/javascript">
                $(document).ready(function(){
                    var headLine = $(".email_headline").html();
                    $("#addNewHeadline").click(function(){
                        $("#emarketing_headlines").append("<hr>").append(headLine);
                    });
                });
            </script>
            <hr/>
            <ul class="edit_details">
                <li>Additional Notes:<input type="text" class="form-control" name="additional_notes"/></li>
                <li>Close Date:<input type="date" class="form-control" name="close_date"/></li>
                <li>Order Processing/Print By Date:<input type="date" class="form-control" name="processing_date"/></li>
                <li>In Hands Date:<input type="date" class="form-control" name="in_hands_date"/></li>
                <li>Ship By Date:<input type="date" class="form-control" name="ship_by_date"/></li>
                <li>Ship From: <select required="required" class="form-control" name="ship_from">
                    <option name="OR" value='OR'>OR</option>
                    <option name="MO" value='MO'>MO</option></select></li>
            </ul>
            <br/>
            <button type="submit" class="btn btn-primary">Submit E-marketing</button>
        </form>
    </body>
</html>