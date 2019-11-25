<?php
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
    include("includes/inc-dbc-conn.php");

    function send_notification($column, $data, $product_id, $user_name){
        include("includes/inc-dbc-conn.php");
        $user_list = array(1,10,11,12);
        $output_text = $user_name . " updated " . $column . " with " . $data;
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
    if(isset($_GET['product_id'])){
        //update database
        $column_to_update = $_GET["column_to_update"];
        $data = $_GET["data"];
        $product_id = $_GET["product_id"];
        $user_name = $_GET["username"];

        $response = "";

        $update_query = "UPDATE `admin_client_products` SET " . $column_to_update . "=\"" . $data . "\" WHERE _id=" . $product_id;

        if($conn->query($update_query) === TRUE){
            $response .="Updated";
            //add record to product change time series
            $tracking_query = "INSERT INTO `admin_product_changes` (product_id, username, product_changes) VALUES (\"" 
            . $product_id . "\", \"" . $user_name . "\", \"" . $column_to_update . " > " . $data .  "\");";

            if($conn->query($tracking_query) === TRUE){
                $response .= " and Tracked";
                send_notification($column_to_update,$data,$product_id,$user_name);
            }
            else{
                $response .= " but Tracking Failed - " . mysqli_error($conn);
            }
        }
        else{
            
            $response .="Update Failed - " . mysqli_error($conn);
        }
        echo $response;
    }
?>