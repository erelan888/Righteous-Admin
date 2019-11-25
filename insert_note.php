<?php
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
    include_once("includes/inc-dbc-conn.php");

    if(isset($_GET['product_id'])){
        
        $product_id = $_GET['product_id'];
        $note_text = mysqli_real_escape_string($conn,$_GET['notes']);
        $query = "INSERT INTO `admin_product_notes` (_product_id, notes) VALUES ('" . $product_id . "','" . $note_text . "');";
        
        if($conn->query($query) === TRUE){
            echo "Updated";
        }
        else{
            echo $query;
            echo mysqli_error($conn);
        }
    }
?>