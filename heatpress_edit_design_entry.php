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
    function add_activity($user_id_param, $activity_text){
        //Adds activity to the activity log
    }
    if(isset($_GET['design_id'])){
        $design_id = $_GET['design_id'];
        $select_query = "SELECT * FROM `admin_heatpress_design_list` WHERE `_id`=" . $design_id;

        $result = mysqli_query($conn, $select_query);
        $design = mysqli_fetch_assoc($result);

        $design_name     = $design['heatpress_design_name'];
        $client_name     = $design['customer_name'];
        $file_name       = $design['embroidery_file_name'];
        $date            = $design['design_date'];
        $image_url       = $design['image_url'];
    }
    ?>
    <html>
        <head>
        <title>Edit Heatpress Design - RCHQ Admin</title>
        <meta name="robots" content="noindex,nofollow"/>
        <?php
            include_once("includes/inc-html-header.php");
        ?>
        </head>
        <body>
        <?php include_once("includes/inc-header.php"); ?>
        <div class="container skip-nav">
            <h1 style="padding-bottom: 20px;">Edit Embroidery Design</h1>
            <p>
            <a href="https://admin.authenticmerch.com/heatpress_design_list.php" class="btn"> <i class="fas fa-undo"></i> Back To Design List</a>    
            <a href="https://admin.authenticmerch.com/heatpress_delete_design_entry.php?design_id=<?php echo $design_id; ?>" class="btn"> <i class="fas fa-trash-alt"></i> Delete This Design</a>
            <a class="btn" onclick="$('#attachment_form').toggle(300);" style="cursor:pointer;"><i class="far fa-image"></i> Update Image</a>
            </p>
            <hr/>
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
                <form name="add_design" method="POST" action="">
                    <ul id="image-block">
                        <li>
                            <img src='<?php echo $image_url; ?>'/><br/>
                            <?php
                                if(!empty($image_url)){
                            ?>
                                    <a style="cursor: pointer;" class="delete_image_button">Delete Image</a>
                            <?php
                                }
                            ?>
                        </li>
                    </ul>
                    <br/>
                    <hr style="clear:both;"/>
                    <ul class="edit_details">
                        <li>Design Name: <input type="text" class="form-control" value="<?php echo str_replace("\\","",str_replace("\"","&quot;",$design_name)); ?>" name="embroidery_design_name"/></li>
                        <li>Client Name: <input type="text" class="form-control" value="<?php echo $client_name; ?>" name="client_name"/></li>
                        <li>File Name: <input type="text" class="form-control" value="<?php echo $file_name; ?>" name="file_name"/></li>
                        <li>Date: <input type="date" class="form-control" value="<?php echo $date; ?>" name="date"/></li>
                    </ul>
                </form>

                <!-- CHANGE HISTORY FOR DESIGN -->
                <div class="dashboard-block client-block">
                    <hr>
                        <h3>Change History</h3>
                        <ul class="edit_details">
                            <?php
                                $change_query = "SELECT * FROM `admin_heatpress_changes` WHERE design_id=" . $design_id;
                                $results = mysqli_query($conn, $change_query);
                                while($change = mysqli_fetch_assoc($results)){
                                    echo "<li><b>" . $change["username"] . "</b><br><i>" . $change["design_changes"] . "</i> - " . $change["date"] . "</li>";
                                }
                            ?>
                        </ul>
                    </div>
                <!-- END CHANGE HISTORY FOR DESIGN -->
            </div>
            <?php include_once("includes/inc-footer.php"); ?>
            <script type="text/javascript">
                        $(document).ready(function(){
                            $(".form-control").change(function(){
                                var username = "<?php echo $username; ?>";
                                var design_id = <?php echo $design_id; ?>;
                                var column_to_update = $(this).attr('name');
                                var data = encodeURIComponent($(this).val());
                                
                                var urlData = "update_heatpress_details.php?"
                                    + "username=" + username
                                    + "&design_id=" + design_id
                                    + "&column_to_update=" + column_to_update
                                    + "&data=" + data;

                                console.log(urlData);

                                $.ajax({
                                    url: urlData,
                                    success: function (data) {
                                        // Notify User
                                        // Refresh Tracked Changes
                                        alert(data);
                                    },
                                    error: function (xhr, status, error) {
                                        if (xhr.status > 0){
                                            alert(status); // status 0 - when load is interrupted
                                        } 
                                    }
                                });
                            }); /* END form-control*/
                            $(".delete_image_button").click(function(){
                                var username = "<?php echo $username; ?>";
                                var design_id = <?php echo $design_id; ?>;
                                var column_to_update = "image_url";
                                var data = "";
                                
                                var urlData = "update_heatpress_details.php?"
                                    + "username=" + username
                                    + "&design_id=" + design_id
                                    + "&column_to_update=" + column_to_update
                                    + "&data=" + data;

                                $("#image_block > li > img").attr("src","");

                                console.log(urlData);

                                $.ajax({
                                    url: urlData,
                                    success: function (data) {
                                        // Notify User
                                        // Refresh Tracked Changes
                                        alert(data);
                                    },
                                    error: function (xhr, status, error) {
                                        if (xhr.status > 0){
                                            alert(status); // status 0 - when load is interrupted
                                        } 
                                    }
                                });
                            }); /* END Delete Image Button */
                        });
                        
                    </script>
                    <!-- Form for adding attachments -->
                    <div id="attachment_form">
                    <div class="X" onclick="$(this).parent().toggle(300);">X</div>
                        <h3>Upload new Image</h3>
                        <form action="update_image.php" method="post" enctype="multipart/form-data" id="image_uploading_form">
                            <input type="hidden" name="design_id" value="<?php echo $design_id; ?>" id="design_id"/>
                            Select image to upload:
                            <input type="file" name="fileToUpload" id="fileToUpload">
                            <input type="submit" value="Upload New Image" name="submit" class="btn btn-primary">
                        </form>
                    </div>
                    <!-- END for for adding attachments -->
        </body>
    </html>