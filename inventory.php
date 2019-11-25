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
     global $client_name;
     $conn = new mysqli($servername, $db_username, $db_password,$db);
     
     // Check connection
     if ($conn->connect_error) {
         die("Connection failed: " . $conn->connect_error);
     } 
    function get_client_url($internal_client_id){
        global $conn;
        global $client_name;
        $url_query = "SELECT _client_id,client_store_url,client_name FROM `admin_client_details` WHERE `_client_id`=" . $internal_client_id;
        $url_results = mysqli_query($conn, $url_query);
        $results = mysqli_fetch_assoc($url_results);

        $client_name = $results['client_name'];
        return $results['client_store_url'];
    }
    function get_stock_type($internal_client_id, $product_title){
        global $conn;

        $product_query = "SELECT client_id, product_stock_ownership FROM `admin_client_products` WHERE `client_id`='" 
        . $internal_client_id . "' AND `product_name` LIKE '%" . mysqli_real_escape_string($conn, $product_title) . "%';";
        $product_results = mysqli_query($conn, $product_query);
        $product = mysqli_fetch_assoc($product_results);

        return trim(strval($product['product_stock_ownership']));
    }
    function get_variation_sales($variation_id, $product_id, $client_id){
        global $conn;

        $date_to = date('Y-m-d H:i:s');

        $until = new DateTime();
        $interval = new DateInterval('P12M');
        $from = $until->sub($interval); 
        $date_from = $from->format('Y-m-d H:i:s');
        //get orders for the last 6 months
        
        $order_number_query = "SELECT number FROM `admin_client_woocommerce_orders` WHERE `client_id`=" 
         . $client_id . " AND date_created BETWEEN '" . $date_from . "' AND '" . $date_to . "';";

        $order_number_results = mysqli_query($conn, $order_number_query);
        $order_list = "";
        while($order = mysqli_fetch_assoc($order_number_results)){
            $order_list .= "'" . $order['number'] . "',";
        }

        $product_sales_query = "";
        if($variation_id != -1){
            $product_sales_query = "SELECT SUM(woo_quantity) as quantity 
                                        FROM `admin_client_order_line_items`  
                                        WHERE `order_id` IN(" . substr_replace($order_list ,"",-1) . ") AND `woo_variation_id`=" . $variation_id;
        }
        else{
            $product_sales_query = "SELECT SUM(woo_quantity) as quantity 
                                        FROM `admin_client_order_line_items`  
                                        WHERE `order_id` IN(" . substr_replace($order_list ,"",-1) . ") AND `woo_product_id`=" . $product_id;
        }
        
        
        //$sales_query = "SELECT SUM(woo_quantity) AS sales FROM `admin_client_order_line_items` WHERE `woo_variation_id`=" . $variation_id . " AND `woo_product_id`=" . $product_id;
        $sales_results = mysqli_query($conn, $product_sales_query);
        $sales = mysqli_fetch_assoc($sales_results);
        return $sales['quantity'];
    }
?>
<html lang="en">
    <head>
        <title>Inventory Admin - RCHQ</title>
        <meta name="robots" content="noindex,nofollow"/>
        <?php
            include_once("includes/inc-html-header.php");
        ?>
    </head>
    <body>
        <?php include_once("includes/inc-header.php"); ?>
        <div class="container skip-nav">
            <?php
                if(isset($_GET['client_id'])){
                    $client_id = $_GET['client_id'];
                    get_client_url($client_id);
                    $basic_inventory_query = "SELECT * FROM `admin_client_woocommerce_legacy_products` WHERE `client_id`=" . $client_id;
                    echo "<h1>" . $client_name . "</h1>";
                    echo "<h3 style=\"color: #D02030; font-style:italic;\">Inventory Admin</h3>";
                    ?>
                    <p>
                        <a href="https://admin.authenticmerch.com/dashboard.php" class="btn"> <i class="fas fa-undo"></i> Back To Dashboard</a> 
                    </p>
                    <hr/>
                    <?php
                    $product_results = mysqli_query($conn, $basic_inventory_query);
                    
                    while($product = mysqli_fetch_assoc($product_results)){
                        if($product['type'] != 'variable' && $product['manage_stock'] == 0){
                            continue;
                        }
                        ?>
                        <div class="product-inventory-block">
                            <img src="" class="inventory-product-image product-<?php echo $product["_id"]; ?>" />
                            <p class="lifetime_sales">Lifetime Product Sales: <b><?php echo $product['total_sales'] ?></b></p>
                            <h3><?php echo $product['name']; ?></h3>
                            <table>
                                <tr>
                                    <td>Price:</td>
                                    <td> <b>$<?php echo $product['woo_price'];?></b></td>
                                </tr>
                                <tr>
                                    <td>SKU:</td>
                                    <td><b><?php echo $product['woo_parent_product_sku'];?></b></td>
                                </tr>
                                <tr>
                                    <td>Managed Stock:</td> 
                                    <td><b><?php echo $product['manage_stock']; ?></b></td>
                                </tr>
                                <?php
                                    if(isset($product['stock_quantity'])){
                                        echo "<tr><td>Stock Quantity:</td><td><b>" . $product['stock_quantity'] . "</b></td></tr>";
                                    }
                                ?>
                            </table>
                        <?php
                        //Insert loop for variations
                        if($product['type'] == "variable"){
                            $variation_query = "SELECT * FROM `admin_client_woocommerce_legacy_variations` WHERE `product_id`=" 
                            . $product['woo_product_id'] . " AND `client_id`=" . $client_id ;
                            $variation_results = mysqli_query($conn, $variation_query);
                                echo "<table class=\"table table-bordered\">";
                                echo "<thead class='thead-dark'><tr><th>ID</th>
                                    <th>Attributes</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                    <th>12 Month Sales</th>
                                    <th>Stock</th>
                                    <th>Months of Coverage</th>
                                    <th>Recommended Order (6 Months)</th></tr></thead>";
                                    $src= "";
                            while($variations = mysqli_fetch_assoc($variation_results)){
                                //if($variations['manage_stock'] != 0){
                                    echo"<tr>";
                                    echo "<td style=\"white-space: nowrap\">" . $variations['product_id'] . "->" . $variations['variation_id'] . "</td>";
                                    echo "<td>" . str_replace("|","<br>\n", $variations['attributes']) . "</td>";
                                    echo "<td>" . $variations['variation_sku'] . "</td>";
                                    echo "<td>$" . $variations['variation_price'] . "</td>";

                                    $sales = get_variation_sales($variations['variation_id'], $product['woo_product_id'],$client_id);

                                    echo "<td>" . ($sales > 0? $sales:0) ."</td>";
                                    echo "<td>" . $variations['stock_quantity'] . "</td>";

                                    $one_month_average = $sales/12;
                                    $coverage = 0;

                                    if($one_month_average !=0){
                                        $coverage = number_format(($variations['stock_quantity'] / $one_month_average),2);
                                    }
                                    echo "<td>" . $coverage . "</td>";
                                    
                                    if($coverage > ($one_month_average * 6)){
                                        $recommended = 0;
                                    }
                                    else{
                                        $recommended = (($one_month_average * 6) - intval($variations['stock_quantity']));
                                    }

                                    echo "<td>" . ($recommended > 0? number_format($recommended,0):0) .  "</td>";
                                    echo "</tr>";

                                    $src= $variations['image_src'];
                                //}
                                ?>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            $(".product-<?php echo $product["_id"]; ?>").attr("src",'<?php echo $src?>');
                                        });
                                    </script>
                                <?php
                            }
                            echo "</table><hr/></div><!-- END product-inventory-block -->";
                            
                            
                        } //END Product type
                        else{
                           // if($product['manage_stock'] == 1){
                                $src="images/righteous-logo.jpg";
                            ?>
                                    <script type="text/javascript">
                                        $(document).ready(function(){
                                            $(".product-<?php echo $product["_id"]; ?>").attr("src",'<?php echo $src?>');
                                        });
                                    </script>
                                <?php

                                echo "<table class=\"table table-bordered\">";
                                echo "<thead class='thead-dark'><tr><th>ID</th>
                                    <th>Attributes</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                    <th>12 Month Sales</th>
                                    <th>Stock</th>
                                    <th>Months of Coverage</th>
                                    <th>Recommended Order</th></tr></thead>";

                                echo"<tr>";
                                echo "<td style=\"white-space: nowrap\">" . $product['woo_product_id'] . "</td>";
                                echo "<td></td>";
                                echo "<td>" . $product['woo_parent_product_sku'] . "</td>";
                                echo "<td>$" . $product['woo_price'] . "</td>";
                                //TODO: Change variation id to -1 and adjust accordingly
                                $sales = get_variation_sales(-1, $product['woo_product_id'],$client_id);

                                echo "<td>" . ($sales > 0? $sales:0) ."</td>";
                                echo "<td>" . $product['stock_quantity'] . "</td>";
                                
                                $one_month_average = $sales/12;
                                $coverage = 0;

                                if($one_month_average !=0){
                                    $coverage = number_format(($product['stock_quantity'] / $one_month_average),2);
                                }
                                echo "<td>" . $coverage . "</td>";
                                
                                if($coverage > ($one_month_average * 6)){
                                    $recommended = 0;
                                }
                                else{
                                    $recommended = (($one_month_average * 6) - intval($product['stock_quantity']));
                                }

                                echo "<td>" . ($recommended > 0? number_format($recommended,0):0) .  "</td>";
                                echo "</tr>";
                           // }
                            echo "</table><hr/></div><!-- END product-inventory-block -->";
                        }
                        
                    }
                }
                else{
                   die("No Client Specified");
                }
            ?>
            <script type="text/javascript">
               /* $(document).ready(function(){
                    $(".inventory-product-image").each(function(){
                        if($(this).attr("src") == ''){
                            $(this).parent().remove();
                        }
                    });
                }); */
            </script>
            </div><!-- END CONTAINER/SKIP NAV -->
    </body>
</html>
<!--  /wp-json/wc/v3/products/<id> -->