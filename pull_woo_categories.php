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

     function pullCategories($client_url, $client_id){
        global $consumer_key;
        global $consumer_secret;
        global $conn;
        //Variations /wp-json/wc/v3/products/categories
        $request_url = $client_url . "/wp-json/wc/v3/products/categories?" 
        . "consumer_key=" . $consumer_key . "&consumer_secret=" . $consumer_secret;
                    
        $ch = curl_init($request_url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");                                                                  
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);                                                                     
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));                                                                                                                   

        $result = curl_exec($ch);

        $result_array = json_decode($result);

        foreach($result_array as $category){
            $insert_query = "INSERT INTO `admin_client_woocommerce_categories` (client_id, category_id, category_title) VALUES ('"
            . $client_id . "','"
            . $category->id . "','"
            . $category->name . "');";

            if($conn->query($insert_query) === TRUE){
                echo "Category Inserted...\n\r<br>";
            }
        }
     }
     function get_client_url($internal_client_id){
        global $conn;
        $url_query = "SELECT _client_id,client_store_url FROM `admin_client_details` WHERE `_client_id`=" . $internal_client_id;
        $url_results = mysqli_query($conn, $url_query);
        $results = mysqli_fetch_assoc($url_results);

        return $results['client_store_url'];
    }

      //get all client id's and output
      $credentials_query = "SELECT * FROM `admin_client_details_api_woocommerce`";

      $cred_results = mysqli_query($conn, $credentials_query);

      while($row = mysqli_fetch_assoc($cred_results)){
        $client_id       = $row['_client_id'];
        $consumer_secret = $row['consumer_secret'];
        $consumer_key    = $row['consumer_key'];

        $client_url = get_client_url($client_id);
        pullCategories($client_url, $client_id);
      }
     ?>