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
     function get_client_url($internal_client_id){
        global $conn;
        $url_query = "SELECT _client_id,client_store_url FROM `admin_client_details` WHERE `_client_id`=" . $internal_client_id;
        $url_results = mysqli_query($conn, $url_query);
        $results = mysqli_fetch_assoc($url_results);

        return $results['client_store_url'];
    }
    function send_notification($product_name, $product_id){
        global $conn;
        $user_list = array(1,10,11,12);
        $output_text = $product_name . " was created on WOO from the Admin Portal";
        foreach ($user_list as $user){
            $insert_query = "INSERT INTO `admin_user_notifications` (internal_product_id, notification_text, destination_user) VALUES ('"
            . $product_id ."','"
            . $output_text ."','"
            .$user ."')";

            if($conn->query($insert_query)=== TRUE){
                continue;
            }
        }
    }
    function check_for_product_type($product){
        if(!empty($product['colors']) && (strpos($product['colors'],",") > -1)){
            return "variable";
        }
        else{
            if($product['size_osfm'] == 1 || $product['size_na'] ==1){
                return "simple";
            }
            else{
                return "variable";
            }
        }
    }
    function get_categories($data, $client_id){
        global $conn;
        if(strpos($data,"|") !== false){
            $temp = explode("|",$data);
            return array("id"=>trim($temp[0]));
        }
        else{
            $category_query = "SELECT * FROM `admin_client_woocommerce_categories` WHERE `client_id`=" . $client_id;
            $category_results = mysqli_query($conn, $category_query);

            while($categories = mysqli_fetch_assoc($category_results)){
                $title       = $categories['category_title'];
                $category_id = $categories['category_id'];

                if(strpos($title,"Uncategorized") !== false){
                    return trim($category_id);
                }
            }
        }
    }
    function create_variations($client_url, $product_id, $base_price, $upcharge){
        global $consumer_key;
        global $consumer_secret;

        ///wp-json/wc/v3/products/<product_id>/variations
        $request_url = $client_url . "/wp-json/wc/v3/products/" . $product_id . "/variations?" . "consumer_key=" 
        . $consumer_key . "&consumer_secret=" . $consumer_secret;

        foreach ($sizes as $size){
            foreach($colors as $color){
                //That JSON though
                //Submit that JSON
                $variation=>'regular_price' = $base_price;

                $attributes = array();
                /*
                    TODO: This is the remaining part for uploading the variations
                */
            }
        }
        //this still needs images and skus when complete
    }
    function get_images($product_id){
        global $conn;
        $image_query   = "SELECT * FROM `admin_product_attachments` WHERE `product_id`=" . $product_id;
        $image_results = mysqli_query($conn, $image_query);
        
        $image[] = array();
        while($images = mysqli_fetch_assoc($image_results)){
            if(isset($images['file_name'])){
                $image[] = array("src"=>"http://admin.authenticmerch.com/" . $images['file_name'] ."\"");
            }
        }
        echo "<pre>" . print_r($image) . "</pre>";
        return $image;
    }
    /*
        /wp-json/wc/v3/products?consumer_key=" + consumerKey + "&consumer_secret=" + consumerSecret;
    */

    if(isset($_GET['product_id'])){
        $product_id      = $_GET['product_id'];
        $client_id       = $_GET['client_id'];
        $consumer_secret = $_GET['consumer_secret'];
        $consumer_key    = $_GET['consumer_key'];

        $product_query   = "SELECT * FROM `admin_client_products` WHERE `_id`=" . $product_id;
        $results         = mysqli_query($conn, $product_query);

        $product_data    = mysqli_fetch_assoc($results);
        $corp_price      = $product_data['pricing_corporate'];
        $retail_price    = $product_data['pricing_retail'];
        $description     = $product_data['product_description'];
        $product_title   = $product_data['product_name'];
        $category        = $product_data['product_categories'];
        $weight          = $product_data['product_weight'];
        $length          = $product_data['product_length'];
        $width           = $product_data['product_width'];
        $height          = $product_data['product_height'];
        $product_type    = check_for_product_type($product_data);

        //TODO: Add Attribute support for variation attributes
        $category = get_categories($category, $client_id);
        
        //that JSON tho...
        $product->type              = $product_type;
        $product->regular_price     = ($retail_price > 0? $retail_price: $corp_price);
        $product->description       = $description;
        $product->short_description = $description;
        $product->categories[]      = $category;
        $product->images[]          = get_images($product_id);
        $product->name              = $product_title;
        
        $product->dimensions->height = $height; 
        $product->dimensions->width  = $width; 
        $product->dimensions->length = $length;
        $product->weight             = $weight;
        
        $upload_me = json_encode($product);

        $client_url = get_client_url($client_id);

        $request_url = $client_url . "/wp-json/wc/v3/products?" . "consumer_key=" 
        . $consumer_key . "&consumer_secret=" . $consumer_secret;
                
        $ch = curl_init($request_url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                    
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                    
            curl_setopt($ch, CURLOPT_POSTFIELDS, $upload_me);                   
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                     
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
                'Content-Type: application/json',                                                                                
                'Content-Length: ' . strlen($upload_me))                                                                       
            );                                                                                                                                                                 

        $result = curl_exec($ch);
        curl_close($ch);

        $return = json_decode($result);
        if(isset($return->id)){
            //change uploaded to 1 and redirect
            $update_query = "UPDATE `admin_client_products` SET product_uploaded='1' WHERE _id=" . $product_id;
            if($conn->query($update_query)=== TRUE){
                send_notification($product_title, $product_id);
                if($product_type == "variable"){
                    create_variations($client_url, $return->id, $product->regular_price, $product_data['pricing_size_upcharge']);
                }
                header("Location: https://admin.authenticmerch.com/edit_product.php?product_id=" . $product_id . "&message=Upload%20Successful&message_type=success");
            }
            else{
                echo mysqli_error($conn);
            }
        }
        else{
            print("<pre>". print_r($return,true) ."</pre>");
            print("<pre>". print_r($upload_me,true)."</pre>");
        }
    }
    else{
        echo "Not finding the producty thing";
    }

?>