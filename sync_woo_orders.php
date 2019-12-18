<?php   
    //error_reporting( E_ALL );
    //ini_set( "display_errors", 1 );

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
    function get_client_url($internal_client_id){
        global $conn;
        $url_query = "SELECT _client_id,client_store_url FROM `admin_client_details` WHERE `_client_id`=" . $internal_client_id;
        $url_results = mysqli_query($conn, $url_query);
        $results = mysqli_fetch_assoc($url_results);

        return $results['client_store_url'];
    }
    function get_api_data($client_url, $page){
        global $consumer_key;
        global $consumer_secret;

        $request_url = $client_url . "/wp-json/wc/v3/orders?" 
        . "consumer_key=" . $consumer_key . "&consumer_secret=" . $consumer_secret . "&per_page=20&after=2019-01-01T00:00:00&status=completed,processing&page=". $page ;
        
        //echo "Request URL: " . $request_url . "<br>\n";
        $ch = curl_init($request_url);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                  
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                                                                     
              curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));                                                                                                                   

        $result = curl_exec($ch);
        curl_close($ch);
        $result_array = json_decode($result);
        return $result_array;
    }
    function check_for_order($client_id,$full_order_number){
        global $conn;
        $order_check = "SELECT * FROM `admin_client_woocommerce_orders` WHERE `client_id`=" . $client_id . " AND `number`='" . $full_order_number . "';";
        $check_results = mysqli_query($conn, $order_check);

        if(mysqli_num_rows($check_results) > 0){
            return TRUE;
        }
        else{
            return FALSE;
        }
    }
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
function upload_tax_lines(){
    //TODO
}
function upload_shipping_lines(){
    //TODO
}
function upload_fee_lines(){
    //TODO
}
function upload_coupon_lines(){
    //TODO
}
function upload_refunds(){
    //TODO
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

    if(isset($_GET['client_id'])){
        $credentials_query = "SELECT * FROM `admin_client_details_api_woocommerce` WHERE `_client_id`=".  $_GET['client_id'];
    }
    else{
        $credentials_query = "SELECT * FROM `admin_client_details_api_woocommerce`";
    }

    $loop_results    = mysqli_query($conn, $credentials_query);
    while($credentials = mysqli_fetch_assoc($loop_results)){
        $consumer_secret = $credentials['consumer_secret'];
        $consumer_key    = $credentials['consumer_key'];
        $client_id       = $credentials['_client_id'];

        $client_url = get_client_url($client_id);

        $page = 1;
        $stop_loop = false;
        
        $result_array = get_api_data($client_url,$page);

        while($stop_loop != true){
            foreach($result_array as $order){
                if(check_for_order($client_id,$order->number) === FALSE){
                    upload_order($client_id, $order);
                    upload_billing_details($order->number,$order->billing, $order->shipping);
                }
                else if(check_for_billing_data($order->number) === FALSE){
                    upload_billing_details($order->number,$order->billing, $order->shipping);
                }
                foreach($order->line_items as $item){
                    if(check_for_line_item($order->number, $item->id) === FALSE){
                        upload_line_item($order->number, $item);
                    }
                }
            }
            $page++;
            $result_array = get_api_data($client_url, $page);
            if(empty($result_array))
            {
                echo "Exiting Loop - " . $page;
               $stop_loop = true;
            }         
        }
    }
?>
</table>
<div style="width: 50%; margin: 40px auto;">
    <img src="https://media.giphy.com/media/fdyZ3qI0GVZC0/giphy.gif"/>
    <h4>Task Complete...</h4>
</div>
<!-- /wp-json/wc/v3/orders -->