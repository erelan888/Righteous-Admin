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
         $client_id = $_GET['client_id'];

         $client_name_query = "SELECT client_name FROM `admin_client_details` WHERE _client_id=" . $client_id;
         $client_name_results = mysqli_query($conn, $client_name_query);

         $results = mysqli_fetch_assoc($client_name_results);
         $client_name = $results['client_name'];
     }
?>
<html>
        <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title><?php echo $client_name; ?> Catalog - RCHQ Admin Area</title>
        <meta name="robots" content="noindex,nofollow"/>
        <?php
            include_once("includes/inc-html-header.php");
        ?>
        </head>
        <body>
        <?php 
            $title = "Catalog";    
            include_once("includes/inc-header.php"); 
        ?>
        <div class="container-fluid skip-nav">
            <div class="dashboard-block client-block">
                 <h3 style="padding-bottom: 20px; text-align:center;"><?php echo $client_name; ?> Catalog</h3>
                 <p>
                 <select class="form-control-sm" id="quick-filters" style="float: right; margin: 10px;">
                    <option value="choose_filter">Choose Filter</option>
                    <option value="view_nonstock">View Non-Stock</option>
                    <option value="view_stock">View Stock</option>
                    <option value="view_all">View All</option>
                </select>
                        <a href="https://admin.authenticmerch.com/dashboard.php" class="btn"> <i class="fas fa-undo"></i> Back To Dashboard</a> 
                        <a href="add_product.php?client_id=<?php echo $client_id; ?>" class="btn"><i class="fas fa-plus-circle"></i> Add a New PDS</a>
                    </p>
            <table class="table products">
                <thead>
                    <th>Product Name</th>
                    <th>Vendor</th>
                    <th>Vendor Product #</th>
                    <th>NET Cost</th>
                    <th>Corp</th>
                    <th>Retail</th>
                    <th>Who Owns</th>
                    <th>Ships From</th>
                    <th>Discontinued</th>
                    <th>Links</th>
                    
                </thead>
                <?php
                global $conn;
                    $product_query = "SELECT * FROM `admin_client_products` WHERE `client_id`=" . $client_id . " ORDER BY product_name ASC";
                    $product_results = mysqli_query($conn, $product_query);

                    while($product_details = mysqli_fetch_assoc($product_results)){
                        $product_id              = $product_details['_id'];
                        $product_name            = $product_details['product_name'];
                        $vendor                  = $product_details['vendor'];
                        $vendor_product_number   = $product_details['vendor_product_number'];
                        $pricing_cost_net        = $product_details['pricing_cost_net'];
                        $pricing_corporate       = $product_details['pricing_corporate'];
                        $pricing_retail          = $product_details['pricing_retail'];
                        $product_stock_ownership = $product_details['product_stock_ownership'];
                        $product_ships_from      = $product_details['product_ships_from'];
                        $is_discontinued         = $product_details['is_discontinued'];
                ?>
                        <tr class="product_row <?php echo $product_stock_ownership; ?>">
                            <td><a href="edit_product.php?product_id=<?php echo $product_id; ?>&client_id=<?php echo $client_id; ?>" title="Edit Product Info"><?php echo $product_name; ?></a></td>
                            <td><?php echo $vendor; ?></td>
                            <td><?php echo $vendor_product_number; ?></td>
                            <td>$<?php echo $pricing_cost_net; ?></td>
                            <td>$<?php echo $pricing_corporate; ?></td>
                            <td>$<?php echo $pricing_retail; ?></td>
                            <td><?php echo $product_stock_ownership; ?></td>
                            <td><?php echo $product_ships_from; ?></td>
                            <td><?php echo ($is_discontinued == 1 ? "Yes" : "No"); ?></td>
                            <td><a href="edit_product.php?product_id=<?php echo $product_id; ?>&client_id=<?php echo $client_id; ?>" title="Edit Product Info"><i class="fas fa-edit"></i></a>
                        </tr>
                <?php
                    }
                ?>
                </table>
            </div>
    </div>
        <?php include_once("includes/inc-footer.php"); ?>
        <script type="text/javascript">
            $(document).ready(function(){
                $("#quick-filters").on("change",function(e){
                    var valueSelected = this.value;

                    if(valueSelected == "view_all"){
                        $('.product_row').fadeIn(300);
                    }
                    else if(valueSelected == "view_nonstock"){
                        $('.product_row').hide();
                        $('.nonstock').fadeIn(300); 
                    }
                    else if(valueSelected == "view_stock"){
                        $('.product_row').fadeIn(300);
                        $('.nonstock').hide();
                    }
                });
            });
        </script>
    </body>
</html>
     