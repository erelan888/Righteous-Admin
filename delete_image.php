<?php
        error_reporting( E_ALL );
        ini_set( "display_errors", 1 );
        include_once("includes/inc-dbc-conn.php");

        if(isset($_GET['product_id'])){
            $file_name  = $_GET['file_name'];
            $product_id = $_GET['product_id'];

            $delete_query = "DELETE FROM `admin_product_attachments` WHERE `file_name`='" . $file_name . "';";
            if($conn->query($delete_query) === TRUE){
                header("Location: https://admin.authenticmerch.com/edit_product.php?product_id=" . $product_id);
            }
            else{
                die ("Failed to delete image: " . mysqli_error($conn));
            }
        }
?>