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

        $request_url = $client_url . "/wp-json/wc/v3/products/attributes?" 
        . "consumer_key=" . $consumer_key . "&consumer_secret=" . $consumer_secret . "&context=view";
        
        echo "Request URL: " . $request_url . "<br>\n";
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
    function upload_attribute($data, $client_id){
        global $conn;

        $insert_query = "INSERT INTO `admin_client_woocommerce_attributes` (attribute_name, attribute_id, client_id) VALUES ('"
        . $data->name . "','"
        . $data->id . "','"
        . $client_id . "')";

        if($conn->query($insert_query) === TRUE){
            return;
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

        $result_array = get_api_data($client_url,$page);

        foreach($result_array as $attribute){
            upload_attribute($attribute, $client_id);
        }
        $result_array = get_api_data($client_url);    
    }
?>
</table>
<div style="width: 50%; margin: 40px auto;">
    <img src="https://media.giphy.com/media/fdyZ3qI0GVZC0/giphy.gif"/>
    <h4>Task Complete...</h4>
</div>
<!-- /wp-json/wc/v3/products/attributes -->