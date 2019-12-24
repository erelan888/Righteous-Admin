<?php
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
    $date_from = $_GET['date_from'];
    $date_to   = $_GET['date_to'];

    $sales_query = "SELECT * FROM `admin_client_details` WHERE `_client_id`=" . $_GET['client_id'];
    $sales_results = mysqli_query($conn, $sales_query);
    $results = mysqli_fetch_assoc($sales_results);
    
    $client_name = $results['client_name'];
    $client_id   = $results['_client_id'];

    $order_number_query = "";
    if(!empty($date_from) && !empty($date_to)){
        $order_number_query = "SELECT number FROM `admin_client_woocommerce_orders` WHERE `client_id`=" . $client_id . " AND date_created BETWEEN '" . $date_from . "' AND '" . $date_to . "';";
    }
    else{
        die("No Dates set");
    }

    $order_number_results = mysqli_query($conn, $order_number_query);
    $order_list = "";
    while($order = mysqli_fetch_assoc($order_number_results)){
            $order_list .= "'" . $order['number'] . "',";
        }
    $inventory_query = "SELECT woo_parent_product_sku as sku,  woo_price as price, stock_quantity as quantity, 
    FROM `fe32045_admin_catalog.admin_client_woocommerce_legacy_products`
    UNION ALL
    SELECT variation_sku as sku, variation_price as price, stock_quantity as quantity,
    FROM `fe32045_admin_catalog.admin_client_woocommerce_legacy_variations`
    ORDER BY sku;";


    $product_results = mysqli_query($conn, $inventory_query);
    $EOL = "\r\n";
    $output = "SKU, Price, Quantity" . $EOL;
    
    while($product = mysqli_fetch_assoc($product_results)){
        $output .= $product['product_sku'] . ", $" . $product['price'] . "," . $product['quantity'] . $EOL;
    }

    echo $output;
}
else{
    die("Incorrect Info Sent");
}
?>