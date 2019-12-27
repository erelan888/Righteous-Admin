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

    $client_id   = $_GET['client_id'];

    $basic_inventory_query = "SELECT * FROM `admin_client_woocommerce_legacy_products` WHERE `client_id`=" . $client_id;
    $product_results = mysqli_query($conn, $basic_inventory_query);
    $EOL = "\r\n";
    $output = "SKU, Name, Price, Quantity, Attributes" . $EOL;

    while($product = mysqli_fetch_assoc($product_results)){
        $name = $product['name'];
        if($product['type'] != 'variable'){
            $output .= $product['woo_parent_product_sku'] . "," . str_replace(",","",$name) . ",$" . $product['woo_price'] . "," . $product['stock_quantity'] . $EOL; 
        }
        else{
            //variable product get variations instead
            $variation_query = "SELECT * FROM `admin_client_woocommerce_legacy_variations` WHERE `product_id`=" 
                            . $product['woo_product_id'] . " AND `client_id`=" . $client_id ;
            $variation_results = mysqli_query($conn, $variation_query);
            while($variations = mysqli_fetch_assoc($variation_results)){
                $output .= $variations['variation_sku'] . "," . str_replace(",","",$name) . ",$" . $variations['variation_price'] . "," . (empty($variations['stock_quantity'])?"":$variations['stock_quantity']) . "," . $variations['attributes'] . $EOL; 
            }
        }
    }
    echo $output;
}
else{
    die("Incorrect Info Sent");
}
?>