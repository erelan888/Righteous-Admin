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
     $conn = new mysqli($servername, $db_username, $db_password,$db);
     
     // Check connection
     if ($conn->connect_error) {
         die("Connection failed: " . $conn->connect_error);
     } 

   /* function generate_sku($client_name, $style_type, $category_type, $submitted_sizes){
        //Generate the sku based on the submitted values
    } */
    function get_client_id($client_name){
        global $conn;
        $client_id_query = "SELECT _client_id FROM `admin_client_details` WHERE `client_name`='" . mysqli_real_escape_string($conn,$client_name) . "';";
        $results = mysqli_query($conn,$client_id_query);
        while($row = mysqli_fetch_assoc($results)){
            return $row['_client_id'];
        }
    }
    function email_PDS_update($email, $user_cc,$email_subject,$output){
        $headers = array("From: embroidery@authenticmerch.com",
            "Reply-To: no-reply@authenticmerch.com",
            "Content-type:text/html;charset=UTF-8",
            "CC: " . $user_cc,
            "X-Mailer: PHP/" . PHP_VERSION );

        $headers = implode("\r\n", $headers);

        mail($email,$email_subject,$output,$headers);
    }
    function add_activity($user_id_param, $activity_text){
        //Adds activity to the activity log
    }
    function assign_new_file_number(){
        global $conn;
        $select_query = "SELECT * FROM `admin_embroidery_design_list`;";
        $file_names = mysqli_query($conn, $select_query);

        $highest_number = 0;
        while($file = mysqli_fetch_assoc($file_names)){
            $number = intval($file['embroidery_file_name']);
            if($number > $highest_number){
                $highest_number = $number;
            }
        }
        return $highest_number + 1;
    }

    if(isset($_POST["design_name"])){
        $design_name     = mysqli_real_escape_string($conn, $_POST['design_name']);
        $client_name     = mysqli_real_escape_string($conn, $_POST['client_name']);
        $file_name       = mysqli_real_escape_string($conn, $_POST['file_name']);
        $stitch_count    = mysqli_real_escape_string($conn, $_POST['stitch_count']);
        $disk_number     = mysqli_real_escape_string($conn, $_POST['disk_number']);
        $date            = mysqli_real_escape_string($conn, $_POST['date']);
        $sales_rep       = mysqli_real_escape_string($conn, $_POST['sales_rep']);
        $old_file_name   = mysqli_real_escape_string($conn, $_POST['old_file_name']);
        $old_file_number = mysqli_real_escape_string($conn, $_POST['old_file_number']);
        $size            = mysqli_real_escape_string($conn, $_POST['size']);
        $in_progress     = $_POST['in_progress'];

        //Image handling
        $target_dir = "uploads/embroidery/";
        $target_file = $target_dir . basename($_FILES['fileToUpload']["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
       
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if($check == false) {
            $uploadOk = 0;
        } 
        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists. Please rename your image.";
            $uploadOk = 0;
        }
        if ($_FILES["fileToUpload"]["size"] > 4000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }
        // Allow certain file formats
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
            move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
        }
        //END Image Handling

        $insert_query = "INSERT INTO `admin_embroidery_design_list` (customer_name,embroidery_file_name,embroidery_design_name, stitch_count,
        disk_number, design_date, sales_rep, old_file_name, old_disc_number, size, image_url) VALUES ('"
        . $client_name . "','"
        . $file_name . "','"
        . $design_name . "','"
        . $stitch_count . "','"
        . ($disk_number==''?"0":$disk_number) . "','"
        . date("Y-m-d", strtotime($date)) . "','"
        . $sales_rep . "','"
        . $old_file_name . "','"
        . $old_file_number . "','"
        . $size . "','"
        . $target_file . "');";

        if($conn->query($insert_query) === TRUE){
            $message = "Success! Return to the <a href='https://admin.authenticmerch.com/embroidery_design_list.php'>full list</a>, or add another below.";
            $message_type = "success";
        }
        else{
            $output = $insert_query . " \n\nERROR: " . mysqli_error($conn);
            email_PDS_update("dustin@rchq.com",null,"Error Creating Embroidery Design",$output);

            $message = "Insert Failed! Error message has been sent to Dustin to address. Please check with him in a few minutes.";
            $message_type = "fail";
            echo $output;
        }
    }

    ?>
    <html>
        <head>
        <title>Add Embroidery Design - RCHQ Admin</title>
        <meta name="robots" content="noindex,nofollow"/>
        <?php
            include_once("includes/inc-html-header.php");
        ?>
        </head>
        <body>
        <?php include_once("includes/inc-header.php"); ?>
        <div class="container skip-nav">
            <h1 style="padding-bottom: 20px;">Create New Embroidery Design</h1>
            <div class="dashboard-block client-block">
                    <?php
                        if(!empty($message) and $message_type == "fail"){
                    ?>
                            <p class="alert alert-danger"><?php echo $message; ?></p>
                    <?php
                        } 
                        else if (!empty($message) and $message_type == "success"){
                    ?>
                            <p class="alert alert-success"><?php echo $message; ?></p>
                    <?php
                        }
                    ?>
                <form name="add_design" method="POST" action="" enctype="multipart/form-data">
                    <hr/>
                    <ul class="edit_details">
                        <li><input type="file" name="fileToUpload" id="fileToUpload"></li>
                        <li>Design Name*: <input type="text" class="form-control" value="" name="design_name" required="required"/></li>
                        <li>Client Name*: <input type="text" class="form-control" value="" name="client_name" required="required"/></li>
                        <li>File Name*: <input type="text" class="form-control" value="<?php echo assign_new_file_number();?>" name="file_name" required="required"/></li>
                        <li>Stitch Count*: <input type="text" class="form-control" value="" name="stitch_count" placeholder="Enter 0 if you dont know the count yet"/></li>
                        <li>Disk #: <input type="text" class="form-control" value="" name="disk_number"/></li>
                        <li>Date: <input type="date" class="form-control" value="" name="date"/></li>
                        <li>Rep*: <input type="text" class="form-control" value="" name="sales_rep" required="required"/></li>
                        <li>Old File Name: <input type="text" class="form-control" value="" name="old_file_name"/></li>
                        <li>Old File Number (Not Used): <input type="text" class="form-control" value="" name="old_file_number"/></li>
                        <li>Size (HxW): <input type="text" class="form-control" value="" name="size"/></li>
                        <li>In Progress?: <select class="form-control" name="in_progress">
                            <option  name="No" value="0">No</option><option name="Yes" value="1">Yes</option></select>
                    </ul>
                    <br>
                    <input type="submit" value="Save New Design" class="btn btn-primary"/>
                </form>
            </div>
            <?php include_once("includes/inc-footer.php"); ?>
        </body>
    </html>