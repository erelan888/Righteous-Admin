<?php

    ob_start();
    session_start();
    
    $user_id = $_SESSION["user_id"];
    $username = $_SESSION['username'];
    $permission_level = $_SESSION["permission_level"];
    if(!$user_id){
        header("Location: https://admin.authenticmerch.com");
    }
    else{
        //add_activity($user_id,"Visited Dashboard");
    }
    function send_notification($product_id, $user_name){
        include("includes/inc-dbc-conn.php");
        $user_list = array(1);
        $output_text = $user_name . " updated the images for a product";
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

    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
    include_once("includes/inc-dbc-conn.php");
    
    var_dump($_FILES);
    $target_dir = "uploads/";
    $target_file = basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    // Check if image file is a actual image or fake image
    if(isset($_POST["submit"])) {
        echo "POST Submitted\n";
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        $product_id = $_POST["product_id"];
        if($check !== false) {
            $uploadOk = 1;
        } 
        else {
            echo "File is not an image.";
            $uploadOk = 0;
        }
    }
    // Check if file already exists
    $count = 1;
    while(file_exists("uploads/" . $target_file))
    {
        if (file_exists("uploads/" . $count . "_" . $target_file)) { 
            echo "uploads/" . $count . "_" . $target_file;
            $count++;
        }
        else{
            $target_file =  $count . "_" . $target_file;
            echo $target_file;
            break;
        }
    }

    $target_file = $target_dir . $target_file;
    echo "Checking if file is the right size...\n";
    // Check file size - limit 4mb
    if ($_FILES["fileToUpload"]["size"] > 4000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    echo "Checking file format...\n";
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif" ) {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } 
    else {
        echo "Preparing upload - " . $target_file;
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            //file uploaded
            //add to database for product id
            $insert_query = "INSERT INTO `admin_product_attachments`(product_id, file_name) VALUES ('" . $product_id . "','" . $target_file . "')";
            if($conn->query($insert_query) === TRUE){
                send_notification($product_id, $username);
                header("Location: https://admin.authenticmerch.com/edit_product.php?product_id=" . $product_id);
            }
            else{
                die("Failed to insert image -> " . mysqli_error($conn));
            }
        } 
        else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
?>