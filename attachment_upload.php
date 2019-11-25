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
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
    include_once("includes/inc-dbc-conn.php");

    echo "Outputing FILES variable\n";
    print_r($_FILES);
    echo "\nOutputing POST variable\n;"
    print_r($_POST);
    
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
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
    echo "Checking if file exists....\n";
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
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
        echo "Preparing upload";
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            //file uploaded
            //add to database for product id
            $insert_query = "INSERT INTO `admin_product_attachments`(product_id, file_name) VALUES ('" . $product_id . "','" . $target_file . "')";
            if($conn->query($insert_query) === TRUE){
                header("Location: https://admin.authenticmerch.com/edit_product.php?product_id=" . $product_id);
            }
            else{
                echo $insert_query;
                die("Failed to insert image -> " . mysqli_error($conn));
            }
        } 
        else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
?>