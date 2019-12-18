<?php
    ob_start();
    session_start();
    
    define("PHP_EOL_FIX","\r\n");
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
    
    if(isset($_GET['type'])){
        $upload_type = $_GET['type'];
        
        $omega_query = "SELECT * FROM `admin_upload_file_queue`";
        $result = mysqli_query($conn, $omega_query);

        $output = "_id,order_id,line_item_id,name,woo_product_id,woo_variation_id,woo_quantity,tax_class,subtotal,total_tax,product_sku,price,full_order_number,billing_first_name,billing_last_name,billing_company,billing_address_1,billing_address_2,billing_city,billing_state,billing_postcode,billing_country,billing_email,billing_phone,shipping_first_name,shipping_last_name,shipping_company,shipping_address_1,shipping_address_2,shipping_city,shipping_state,shipping_postcode,shipping_country,_id,client_id,number,version,status,currency,date_created,date_modified,discount_total,discount_tax,shipping_total,shipping_tax,cart_tax,total,customer_id,customer_ip_address,payment_method,payment_method_title,date_paid,date_completed,client_name,upload_name" . PHP_EOL_FIX;

        while($line = mysqli_fetch_assoc($result)){
            //this makes account funds, cost center id's Purchase Order
            if($line['payment_method_title'] != "Purchase Order" && $line['payment_method_title'] != "Credit Card"){
                $line['payment_method_title'] = "Purchase Order";
            }
            //Makes sure everything thats supposed to say Credit Card, says credit card
            if(strpos($line['payment_method'], 'authnet') !== false || strpos($line['payment_method'],'authorize_net_cim_credit_card') !== false){
                $line['payment_method_title'] = "Credit Card";
            }
            //check for employee payroll deduct - These do not need to be included in quickbooks from this upload. They are handled in billing logs later
            if($line['client_id'] == 9 || $line['client_id'] == 20){
                if(strpos($line["payment_method"],'cost_center_id') !== false){
                    continue;                    
                }
            }
            if($line['payment_method_title'] == $upload_type){
                $line['name'] = str_replace(":","",str_replace(",","",substr($line['name'],0,27).'...'));
                $line['billing_company'] = str_replace(",","",$line['billing_company']);
                $line['shipping_address_1'] = str_replace(",","",$line['shipping_address_1']);
                $line['shipping_company'] = str_replace(",","",$line['shipping_company']);

                $temp_date = strtotime($line['date_created']);
                $line['date_created'] = date('m/d/Y',$temp_date);

                //put all names on one line
                $line['billing_first_name']  = $line['billing_first_name'] . " " . $line['billing_last_name'];
                $line['shipping_first_name'] = $line['shipping_first_name'] . " " . $line['shipping_last_name'];

                unset($line['client_code']);
                unset($line['client_stock_held']);
                unset($line['client_store_number']);
                unset($line['client_store_url']);
                unset($line['client_handles_orders']);
                unset($line['client_inventory']);
                unset($line['client_special_projects']);
                unset($line['client_who_owns_stock']);
                unset($line['contract_renewal_date']);
                unset($line['last_line_review']);

                $output .= implode(",",$line);
                $output .= PHP_EOL_FIX;
            }
        }

        echo $output;
    }
     
?>