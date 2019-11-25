<?php
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
    include_once("includes/inc-dbc-conn.php");

    if(isset($_GET['design_id'])){
        
        $product_id = $_GET['design_id'];
        $query = "DELETE FROM `admin_embroidery_design_list` WHERE `id`=" . $design_id;
        
        if($conn->query($query) === TRUE){
            header("Location:https://admin.authenticmerch.com/embroidery_design_list.php");
        }
        else{
            die("DELETE Failed...");
        }
    }
?>