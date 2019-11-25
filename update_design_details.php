<?php
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
    include_once("includes/inc-dbc-conn.php");

    if(isset($_GET['design_id'])){
        //update database
        $column_to_update = $_GET["column_to_update"];
        $data = $_GET["data"];
        $design_id = $_GET["design_id"];
        $user_name = $_GET["username"];

        $response = "";

        $update_query = "UPDATE `admin_embroidery_design_list` SET " . $column_to_update . "=\"" . $data . "\" WHERE _id=" . $design_id;

        if($conn->query($update_query) === TRUE){
            $response .="Updated";
            //add record to product change time series
            $tracking_query = "INSERT INTO `admin_design_changes` (design_id, username, design_changes) VALUES (\"" 
            . $design_id . "\", \"" . $user_name . "\", \"" . $column_to_update . " > " . $data .  "\");";

            if($conn->query($tracking_query) === TRUE){
                $response .= " and Tracked";
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