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
     function email_PDS_update($email, $user_cc,$email_subject,$output){
        $headers = array("From: qb-data-stage@authenticmerch.com",
            "Reply-To: no-reply@authenticmerch.com",
            "Content-type:text/html;charset=UTF-8",
            "CC: " . $user_cc,
            "X-Mailer: PHP/" . PHP_VERSION );

        $headers = implode("\r\n", $headers);

        mail($email,$email_subject,$output,$headers);
    }
     function fetch_orders($date_from, $date_to){
        global $conn;
        $orders_query = "SELECT number FROM `admin_client_woocommerce_orders` WHERE date_created BETWEEN '" . $date_from . "' AND '" . $date_to . "' GROUP BY _id;";
        $results = mysqli_query($conn, $orders_query);

        return $results;
     }
     function insert_order_line_data($order_number){
        global $conn;

        $order_line_item_query = "SELECT * FROM `admin_client_order_line_items` WHERE order_id='" . $order_number . "';";
        $line_items = mysqli_query($conn, $order_line_item_query);

        if(empty($line_items)){
            echo mysqli_error($conn) . "<br>";
        }

        while($line_item = mysqli_fetch_assoc($line_items)){
            $insert_query = "INSERT INTO `admin_upload_file_queue` (order_id,line_item_id,name,woo_product_id,woo_variation_id,woo_quantity,tax_class,
            subtotal,total_tax,product_sku,price) VALUES ('"
            . $line_item['order_id'] . "','"
            . $line_item['line_item_id'] . "','"
            . mysqli_real_escape_string($conn, $line_item['name']) . "','"
            . $line_item['woo_product_id'] . "','"
            . $line_item['woo_variation_id'] . "','"
            . $line_item['woo_quantity'] . "','"
            . $line_item['tax_class'] . "','"
            . $line_item['subtotal'] . "','"
            . $line_item['total_tax'] . "','"
            . $line_item['product_sku'] . "','"
            . $line_item['price'] ."')";

            if($conn->query($insert_query) === TRUE){
                attach_customer_data($order_number);
            }
            else{
                echo mysqli_error($conn) . "<br>";
            }
        }
     }
     function attach_customer_data($order_number){
        global $conn;

        $billing_data_query = "SELECT * FROM `admin_client_woocommerce_orders_customer_details` WHERE full_order_number='" . $order_number . "' LIMIT 1;";
        $results = mysqli_query($conn, $billing_data_query);

        while($billing = mysqli_fetch_assoc($results)){
            $update_query = "UPDATE `admin_upload_file_queue` 
            SET billing_first_name=" . "\"" . mysqli_real_escape_string($conn, $billing['billing_first_name']) . "\"," .
            "billing_last_name="  . "\"" . mysqli_real_escape_string($conn, $billing['billing_last_name']) . "\"," .
            "billing_company="    . "\"" . mysqli_real_escape_string($conn, $billing['billing_company']) . "\"," .
            "billing_address_1="  . "\"" . mysqli_real_escape_string($conn, $billing['billing_address_1']) . "\"," .
            "billing_address_2="  . "\"" . mysqli_real_escape_string($conn, $billing['billing_address_2']) . "\"," .
            "billing_city="       . "\"" . mysqli_real_escape_string($conn, $billing['billing_city']) . "\"," .
            "billing_state="      . "\"" . mysqli_real_escape_string($conn, $billing['billing_state']) . "\"," .
            "billing_postcode="   . "\"" . mysqli_real_escape_string($conn, $billing['billing_postcode']) . "\"," .
            "billing_country="    . "\"" . mysqli_real_escape_string($conn, $billing['billing_country']) . "\"," .
            "billing_email="      . "\"" . mysqli_real_escape_string($conn, $billing['billing_email']) . "\"," .
            "billing_phone="      . "\"" . mysqli_real_escape_string($conn, $billing['billing_phone']) . "\"," .
            "shipping_first_name=". "\"" . mysqli_real_escape_string($conn, $billing['shipping_first_name']) . "\"," .
            "shipping_last_name=" . "\"" . mysqli_real_escape_string($conn, $billing['shipping_last_name']) . "\"," .
            "shipping_company="   . "\"" . mysqli_real_escape_string($conn, $billing['shipping_company']) . "\"," .
            "shipping_address_1=" . "\"" . mysqli_real_escape_string($conn, $billing['shipping_address_1']) . "\"," .
            "shipping_address_2=" . "\"" . mysqli_real_escape_string($conn, $billing['shipping_address_2']) . "\"," .
            "shipping_city="      . "\"" . mysqli_real_escape_string($conn, $billing['shipping_city']) . "\"," .
            "shipping_state="     . "\"" . mysqli_real_escape_string($conn, $billing['shipping_state']) . "\"," .
            "shipping_postcode="  . "\"" . mysqli_real_escape_string($conn, $billing['shipping_postcode']) . "\"," .
            "shipping_country="   . "\"" . mysqli_real_escape_string($conn, $billing['shipping_country']) . "\" " . 
            " WHERE `order_id`='"   . $order_number . "';";

            if($conn->query($update_query) === TRUE){
                attach_order_data($order_number);
            }
            else{
                echo mysqli_error($conn) . "<br>";
            }
        }
     }
     function attach_order_data($order){
        global $conn;
        //attach client data from here admin_upload_file_queue
        $orders_query = "SELECT * FROM `admin_client_woocommerce_orders` WHERE number='" . $order . "';";
        $results = mysqli_query($conn, $orders_query);

        while($order = mysqli_fetch_assoc($results)){
            $order_client_id = $order['client_id'];

            $update_query = "UPDATE `admin_upload_file_queue` 
            SET number='"            . $order['number'] . "'," .
            "version='"              . $order['version'] . "'," .
            "status='"               . $order['status'] . "'," .
            "currency='"             . $order['currency'] . "'," .
            "date_created='"         . $order['date_created'] . "'," .
            "date_modified='"        . $order['date_modified'] . "'," .
            "discount_total='"       . $order['discount_total'] . "'," .
            "discount_tax='"         . $order['discount_tax'] . "'," .
            "shipping_total='"       . $order['shipping_total'] . "'," .
            "shipping_tax='"         . $order['shipping_tax'] . "'," .
            "cart_tax='"             . $order['cart_tax'] . "'," .
            "total='"                . $order['total'] . "'," .
            "customer_id='"          . $order['customer_id'] . "'," .
            "customer_ip_address='"  . $order['customer_ip_address'] . "'," .
            "payment_method='"       . $order['payment_method'] . "'," .
            "payment_method_title='" . $order['payment_method_title'] . "'," .
            "date_paid='"            . $order['date_paid'] . "'," .
            "date_completed='"       . $order['date_completed'] . "' " .
            " WHERE order_id='"     . $order['number'] . "';";

            if($conn->query($update_query) === TRUE){
                attach_client_data($order['number'], $order_client_id); 
            }
            else{
                echo mysqli_error($conn) . "<br>";  
            }
        }
     }
     function attach_client_data($order_id, $client_id){
        global $conn;

        $client_data = "SELECT * FROM `admin_client_details` WHERE _client_id=" . $client_id;
        $clients = mysqli_query($conn, $client_data);
        $client = mysqli_fetch_assoc($clients);
        echo "Attaching client data...\r\n<br>";
        $update_query = "UPDATE `admin_upload_file_queue` 
        SET client_name='"       . mysqli_real_escape_string($conn, $client['client_name']) . "'," .
        "upload_name='"          . mysqli_real_escape_string($conn, $client['name_for_upload']) . "' " .
        " WHERE order_id='"     . $order_id . "';";

        if($conn->query($update_query) === FALSE){
            echo mysqli_error($conn) . "<br>";
        }
     }

     if(isset($_GET['date_from'])){
         $date_from = $_GET['date_from'];
         $date_to   = $_GET['date_to'];
     }
     else{
         $date_to   = date("Y-m-d");
         $date_from = date('Y-m-d', strtotime('-7 days'));
     }

    $clear_data_query = "TRUNCATE TABLE `admin_upload_file_queue`";
    if($conn->query($clear_data_query) === TRUE){
        echo "Previous Data Cleared....\r\n<br>"
    }
    echo "Retrieving Order Data...\r\n<br>";
    $orders = fetch_orders($date_from, $date_to);
    if(!empty($orders)){
        foreach($orders as $order){
            echo "Retrieving Data...\r\n<br>";
            echo $order['number'] . "\r\n";
            insert_order_line_data($order['number']);
        }
        echo "Queue Update Complete...\r\n<br>";
    }
    $subject = "Quickbooks data is ready for Upload!";
    $output = "Quickbooks data has been staged for the past 7 days. Please log into <a href='http://admin.authenticmerch.com'>admin.authenticmerch.com</a> and download the files for transaction upload!";
    
        email_PDS_update("customerservice@rchq.com","dustin@rchq.com",$subject, $output);
?>