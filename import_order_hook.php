<?php
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );

     //DB Server info
     $servername = "localhost";
     $db_username = "fe32045_dev_dustin";
     $db_password = "@TbGG3Fdau1m";
     $db = "fe32045_admin_catalog";
     // Create connection
     global $conn;
     global $consumer_key;
     global $consumer_secret;

     $conn = new mysqli($servername, $db_username, $db_password,$db);
     
     // Check connection
     if ($conn->connect_error) {
         die("Connection failed: " . $conn->connect_error);
     }
 /*---------------------------------
 Functions
 -----------------------------------*/
 function upload_order($client_id,$order){
    global $conn;

    $insert_query = "REPLACE INTO `admin_client_woocommerce_orders` (id, client_id, number, version, status, currency, date_created, date_modified, 
    discount_total, discount_tax, shipping_total, shipping_tax, cart_tax, total, total_tax, customer_id, customer_ip_address, payment_method, 
    payment_method_title, date_paid, date_completed) VALUES('"
    . $order->id . "','" 
    . $client_id . "','" 
    . $order->number . "','" 
    . $order->version . "','" 
    . $order->status . "','" 
    . $order->currency . "','" 
    . $order->date_created . "','" 
    . $order->date_modified . "','" 
    . $order->discount_total . "','" 
    . $order->discount_tax . "','" 
    . $order->shipping_total . "','" 
    . $order->shipping_tax . "','" 
    . $order->cart_tax . "','" 
    . $order->total . "','" 
    . $order->total_tax . "','" 
    . $order->customer_id . "','" 
    . $order->customer_ip_address . "','" 
    . $order->payment_method . "','" 
    . $order->payment_method_title . "','" 
    . date ("Y-m-d H:i:s", $order->date_paid) . "','"
    . date ("Y-m-d H:i:s", $order->date_completed) . "');";

    if($conn->query($insert_query) === TRUE){
        echo "Order Uploaded...<br>\n";
    }
    else{
        echo "Upload failed...<br>\n";
        echo mysqli_error($conn) . "<br>\n";
    }
}
function check_for_line_item($full_order_number, $line_item_id){
    global $conn;
    $line_item_check = "SELECT * FROM `admin_client_order_line_items` WHERE `line_item_id`=" . $line_item_id . " AND `order_id`='" . $full_order_number . "';";
    $check_results = mysqli_query($conn, $line_item_check);

    if(mysqli_num_rows($check_results) > 0){
        return TRUE;
    }
    else{
        return FALSE;
    }
}
function check_for_billing_data($full_order_number){
    global $conn;
    $billing_check = "SELECT * FROM `admin_client_woocommerce_orders_customer_details` WHERE `full_order_number`='" . $full_order_number . "';";
    $check_results = mysqli_query($conn, $billing_check);

    if(mysqli_num_rows($check_results) > 0){
        return TRUE;
    }
    else{
        return FALSE;
    }
}
function upload_line_item($full_order_number, $item){
    global $conn;

    $insert_query = "REPLACE INTO `admin_client_order_line_items` (order_id, line_item_id, name, woo_product_id, 
    woo_variation_id, woo_quantity, tax_class, subtotal, total_tax, product_sku, price) VALUES ('"
    . $full_order_number  . "','"
    . $item->id  . "','"
    . mysqli_real_escape_string($conn,$item->name)  . "','"
    . $item->product_id  . "','"
    . $item->variation_id  . "','"
    . $item->quantity  . "','"
    . $item->tax_class  . "','"
    . $item->subtotal  . "','"
    . $item->total_tax  . "','"
    . $item->sku  . "','"
    . $item->price ."')";

    if($conn->query($insert_query) === TRUE){
        echo "Line Item Uploaded...<br>\n";
    }
    else{
        echo "Line Item Upload failed...<br>\n";
        echo mysqli_error($conn) . "<br>\n";
    }
}
function upload_billing_details($full_order_number, $billing, $shipping){
    global $conn;

    $insert_query = "INSERT INTO `admin_client_woocommerce_orders_customer_details` (full_order_number, billing_first_name, billing_last_name, 
    billing_company, billing_address_1, billing_address_2, billing_city, billing_state, billing_postcode, billing_country, billing_email, billing_phone, 
    shipping_first_name, shipping_last_name, shipping_company, shipping_address_1, shipping_address_2, shipping_city, shipping_state, shipping_postcode, shipping_country) VALUES ('"
    . $full_order_number ."','"
    . $billing->first_name  ."','"
    . $billing->last_name  ."','"
    . mysqli_real_escape_string($conn,$billing->company)  ."','"
    . $billing->address_1  ."','"
    . $billing->address_2  ."','"
    . $billing->city  ."','"
    . $billing->state ."','"
    . $billing->postcode ."','" 
    . $billing->country  ."','"
    . $billing->email ."','"
    . $billing->phone  ."','"
    . $shipping->first_name ."','"
    . $shipping->last_name ."','"
    . mysqli_real_escape_string($conn,$shipping->company) ."','"
    . $shipping->address_1 ."','"
    . $shipping->address_2 ."','"
    . $shipping->city ."','"
    . $shipping->state ."','"
    . $shipping->postcode ."','"
    . $shipping->country . "');";

    if($conn->query($insert_query) === TRUE){
        echo "Shipping & Billing Details Uploaded...<br>\n";
    }
    else{
        echo $insert_query . "<br>\n";
        echo "Shipping & Billing Details Upload failed...<br>\n";
        echo mysqli_error($conn) . "<br>\n";
    }
}
function get_client_id($order_number){
    global $conn;
    $data = explode("-",$order_number);
    //0->"os" 1->Store Number 2->Order Number
    $client_query = "SELECT * FROM `admint_client_details` WHERE `client_store_number`= " . $data[1];
    $clients_results = mysqli_query($conn, $client_query);
    $client = mysqli_fetch_assoc($clients_results);

    return $client['_client_id'];
}
/*-------------------------------
Begin Logic
---------------------------------*/
/*
$json = file_get_contents('php://input');
$action = json_decode($json, true);
$output = "Starting data... \n";
foreach($action as $value){
    $output .= $value . "\n";
}

$fWrite = fopen("order-create-log.txt","a");
$wrote = fwrite($fWrite, $output);
fclose($fWrite);
*/

    $output = "Order Incoming Hook<br>";
    
    $json = file_get_contents('php://input');
    $order = json_decode($json, true);
    
    $client_id = get_client_id($order->number);
    
    if(check_for_order($client_id,$order->number) === FALSE){
        upload_order($client_id, $order);
        $output .= "-Upload Orders<br>";
    }

    if(check_for_billing_data($order->number) === FALSE){
        upload_billing_details($order->number,$order->billing, $order->shipping);
        $output .= "-Upload Billing Details<br>";
    }

    foreach($order->line_items as $item){
        if(check_for_line_item($order->number, $item->id) === FALSE){
            upload_line_item($order->number, $item);
            $output .= "-Upload Order Items<br>";
        }
    }

    mail("dustin.gunter88@gmail.com","Import Order Hook",$output); 
     
?>