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
        //add_activity($user_id,"Visited Dashboard");
    }
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
    include_once("includes/inc-dbc-conn.php");

    if(isset($_GET['client_id']) and !isset($_POST['client_name'])){
        
        $client_id = $_GET['client_id'];
        $query = "SELECT * FROM `admin_client_details_api_woocommerce` WHERE `_client_id`=" . $client_id;

        $result = mysqli_query($conn, $query);

        while($row=mysqli_fetch_assoc($result)){
           $consumer_key    = $row["consumer_key"]; 
           $consumer_secret = $row["consumer_secret"];
        ?>
            <html>
                <head>
                <title>Edit Client Credentials - RCHQ Admin Area</title>
                <meta name="robots" content="noindex,nofollow"/>
                <?php
                    include_once("includes/inc-html-header.php");
                ?>
                </head>
                <body>
                <?php include_once("includes/inc-header.php"); ?>
                <div class="container skip-nav">
                    <h1 style="padding-bottom: 20px;">Editing <?php echo $client_name; ?>!</h1>
                    <div class="dashboard-block client-block">
                        <form name="edit_client" method="POST" action="">
                            <input type="hidden" name="_client_id" value="<?php echo $client_id; ?>" />
                            <hr/>
                            <ul class="edit_details">
                                <li>Consumer Key: <input type="text" class="form-control" value="<?php echo $consumer_key; ?>" name="consumer_secret"/></li>
                                <li>Consumer Secret: <input type="text" class="form-control" value="<?php echo $consumer_secret; ?>" name="consumer_key"/></li>
                            </ul>
                            <input type="submit" value="Save Changes" class="btn btn-primary"/>
                        </form>
                    </div>
                    <?php include_once("includes/inc-footer.php"); ?>
                </body>
            </html>
        <?php
        }
    }
    else{
        //check if post is set and update client, then redirect back to dashboard
        if(isset($_POST["consumer_secret"])){
               $client_id       = $_POST['_client_id'];
               $consumer_key    = $_POST["consumer_key"]; 
               $consumer_secret = $_POST["consumer_secret"];
               
            $update_query = "UPDATE `admin_client_details` SET `consumer_key`='" . $consumer_key . 
                "',`consumer_secret`='" . $consumer_secret . 
                "' WHERE `_client_id`=" . $client_id;

            $result = mysqli_query($conn,$update_query);
            if(!$result){
                echo $update_query;
                die("<br>broken!");
            }
            header("Location: https://admin.authenticmerch.com/dashboard.php");
        }
    }
?>