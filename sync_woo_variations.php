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
     global $consumer_key;
     global $consumer_secret;

     $conn = new mysqli($servername, $db_username, $db_password,$db);
     
     // Check connection
     if ($conn->connect_error) {
         die("Connection failed: " . $conn->connect_error);
     } 
    function upload_product_variation($_product_id, $_variation_id, $client_id, $client_url){
        global $conn;
        global $consumer_key;
        global $consumer_secret;
        //check to see if exists, if not upload
        //Variations /wp-json/wc/v3/products/<PRODUCT ID>/variations/<VARIATION ID>
        $request_url = $client_url . "/wp-json/wc/v3/products/" . $_product_id . "/variations/" . $_variation_id . "?" 
        . "consumer_key=" . $consumer_key . "&consumer_secret=" . $consumer_secret . "&page=1" ;
                    
        $ch = curl_init($request_url);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                  
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                                                                     
              curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));                                                                                                                   

        $result = curl_exec($ch);
        curl_close($ch);
        $result_array = json_decode($result);
        $attributes ="";
        foreach ($result_array->attributes as $attribute){
            $attributes .= $attribute->name . "=" . $attribute->option . "|";
        }

        $variation_insert_query = "INSERT INTO `admin_client_woocommerce_legacy_variations` (product_id, variation_id, client_id, variation_sku, variation_price, status,
        purchasable, tax_status, tax_class, manage_stock, stock_quantity, stock_status, backorders, backorders_allowed, backordered, 
        shipping_class, image_src, attributes) VALUES ('"
        . $_product_id . "','"
        . $_variation_id . "','"
        . $client_id . "','"
        . $result_array->sku . "','"
        . (empty($result_array->price)? 0.00:number_format($result_array->price, 2, '.', '')) . "','"
        . $result_array->status . "','"
        . ($result_array->purchasable === FALSE? 0:1) . "','"
        . $result_array->tax_status . "','"
        . $result_array->tax_class . "','"
        . ($result_array->manage_stock === FALSE? 0:1) . "','"
        . ($result_array->stock_quantity === null? 0:$result_array->stock_quantity) . "','"
        . $result_array->stock_status . "','"
        . $result_array->backorders . "','"
        . ($result_array->backorders_allowed === FALSE? 0:1) . "','"
        . ($result_array->backordered === FALSE? 0:1)  . "','"
        . $result_array->shipping_class . "','"
        . $result_array->image->src . "','"
        . $attributes . "')";

        if($conn->query($variation_insert_query) === TRUE){
            sleep(5);
            echo "Variation Uploaded...\n<br>";
        }
        else{
            echo "\n<br>_________________________________\n<br>";
            echo $variation_insert_query;
            echo mysqli_error($conn);
        }
    }
    function check_if_variation_exists($client_id, $_variation_id){
        global $conn;
        $check_query = "SELECT * FROM `admin_client_woocommerce_legacy_variations` WHERE `variation_id`=" . $_variation_id . " AND `client_id`=" . $client_id;
        $results = mysqli_query($conn, $check_query);
        
        echo $check_query . "\n<br>";
        return mysqli_num_rows($results);
    }
    function update_variation_details($client_url, $client_id, $_product_id, $_variation_id){
        global $conn;
        global $consumer_key;
        global $consumer_secret;
        //check to see if exists, if not upload
        //Variations /wp-json/wc/v3/products/<PRODUCT ID>/variations/<VARIATION ID>
        $request_url = $client_url . "/wp-json/wc/v3/products/" . $_product_id . "/variations/" . $_variation_id . "?" 
        . "consumer_key=" . $consumer_key . "&consumer_secret=" . $consumer_secret . "&page=1" ;
                    
        $ch = curl_init($request_url);
              curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                  
              curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                                                                     
              curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));                                                                                                                   

        $result = curl_exec($ch);
        curl_close($ch);
        $result_array = json_decode($result);

        $stock_quantity = $result_array->stock_quantity;
        $price       =  $result_array->price;


        $update_query = "UPDATE `admin_client_woocommerce_legacy_variations` SET(`stock_quantity`=" . (empty($stock_quantity)?0:$stock_quantity) 
                    . ", `variation_price`=" . $price . ") WHERE `client_id`=" . $client_id . " AND `variation_id`=" . $_variation_id;
                
        if($conn->query($update_query) === TRUE){
            echo "Variation Updated...\n<br>";
        }
        else{
            echo "Error Updating Variation: " . mysqli_error($conn);
        }


    }
    function check_if_product_exists($client_id, $_product_id){
        //check to see if the product already exists in the database. returns # of rows returned
        global $conn;
        $check_query = "SELECT * FROM `admin_client_woocommerce_legacy_products` WHERE `woo_product_id`=" . $_product_id . " AND `client_id`=" . $client_id;
        $results = mysqli_query($conn, $check_query);
        
        return mysqli_num_rows($results);
    }
    function get_client_url($internal_client_id){
        global $conn;
        $url_query = "SELECT _client_id,client_store_url FROM `admin_client_details` WHERE `_client_id`=" . $internal_client_id;
        $url_results = mysqli_query($conn, $url_query);
        $results = mysqli_fetch_assoc($url_results);

        return $results['client_store_url'];
    }
    function pull_products($page,$per_page, $client_url, $client_id){
        global $consumer_key;
        global $consumer_secret;
        //Products /wp-json/wc/v3/products
        $request_url = $client_url . "/wp-json/wc/v3/products?" . "consumer_key=" 
        . $consumer_key . "&consumer_secret=" . $consumer_secret . "&per_page=" . $per_page . "&page=" . $page ;
                
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

    if(isset($_GET['client_id'])){
        $client_id = $_GET['client_id'];
        $credentials_query = "SELECT * FROM `admin_client_details_api_woocommerce` WHERE `_client_id`=" . $client_id;

        $cred_results    = mysqli_query($conn, $credentials_query);
        $credentials     = mysqli_fetch_assoc($cred_results);
        $consumer_secret = $credentials['consumer_secret'];
        $consumer_key    = $credentials['consumer_key'];

        $client_url = get_client_url($client_id);
       
        $product_query = "SELECT _id,woo_product_id,client_id,woo_variations FROM `admin_client_woocommerce_legacy_products` WHERE `client_id`=" . $client_id;
        $product_results = mysqli_query($conn, $product_query);

        while($product = mysqli_fetch_assoc($product_results)){
            $product_id = $product['woo_product_id'];
            $variations = explode(',',$product['woo_variations']);
            foreach($variations as $variation){
                if(!empty($variation)){
                    if(check_if_variation_exists($client_id,$variation) < 1){
                        upload_product_variation($product_id, $variation, $client_id, $client_url);
                    }
                }
            }
        }      
    }
    else{
        $credentials_query = "SELECT * FROM `admin_client_details_api_woocommerce`";

        $loop_results    = mysqli_query($conn, $credentials_query);
        while($credentials = mysqli_fetch_assoc($loop_results)){
            $consumer_secret = $credentials['consumer_secret'];
            $consumer_key    = $credentials['consumer_key'];
            $client_id       = $credentials['_client_id'];
            $client_url = get_client_url($client_id);
           
            $product_query = "SELECT _id,woo_product_id,client_id,woo_variations FROM `admin_client_woocommerce_legacy_products` WHERE `client_id`=" . $client_id;
            $product_results = mysqli_query($conn, $product_query);
    
            while($product = mysqli_fetch_assoc($product_results)){
                $product_id = $product['woo_product_id'];
                $variations = explode(',',$product['woo_variations']);
                foreach($variations as $variation){
                    if(!empty($variation)){
                        if(check_if_variation_exists($client_id,$variation) < 1){
                            upload_product_variation($product_id, $variation, $client_id, $client_url);
                        }
                        else{
                            //update data like price and total sales
                            update_variation_details($client_url, $client_id, $product_id, $variation);
                        }
                    }
                }
            }
        }
    }
?>