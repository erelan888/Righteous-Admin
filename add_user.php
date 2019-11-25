<?php
    ob_start();
    session_start();
    
    $user_id = $_SESSION["user_id"];
    $username = $_SESSION['username'];
    if(!$user_id){
        //redirect to login
        header("Location: https://admin.authenticmerch.com");
    }
    else{
        //add_activity($user_id,"Added User");
    }
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
    include_once("includes/inc-dbc-conn.php");

    if(isset($_POST["new_username"])){
        $new_username     = $_POST['new_username'];
        $new_password     = $_POST["new_password"]; 
        $confirm_password = $_POST["confirm_password"];
        $permissions      = $_POST["permissions"];
        $set_permissions = 1;
        if(strcmp($new_password,$confirm_password)){
            if($permissions == "Admin"){
                $set_permissions  = 0;
            }
            $insert_query = "INSERT INTO `admin_login_details` (username, password, user_permissions) VALUES ('" . $username . "','" . $new_password . "','" . $set_permissions . "');";
            $result = mysqli_query($conn,$insert_query);
            if(!$result){
                echo $insert_query;
                die("<br>broken!");
            }
            header("Location: https://admin.authenticmerch.com/dashboard.php");
        }
        else{
            $message = "Password fields need to match";
        }
    }
        ?>
    <html>
        <head>
        <title>Add New User - RCHQ Admin Area</title>
        <meta name="robots" content="noindex,nofollow"/>
        <?php
            include_once("includes/inc-html-header.php");
        ?>
        </head>
        <body>
        <?php include_once("includes/inc-header.php"); ?>
        <div class="container skip-nav">
            <h1 style="padding-bottom: 20px;">Add New User</h1>
            <div class="dashboard-block client-block">
                <form name="edit_user" method="POST" action="">
                    <hr/>
                    <?php
                        if(!empty($message)){
                    ?>
                            <p class="alert alert-danger"><?php echo $message; ?></p>
                    <?php
                        } ?>
                    
                    <ul class="edit_details">
                        <li>New Username: <input type="text" class="form-control" value="" name="new_username"/></li>
                        <li>New Password: <input type="text" class="form-control" value="" name="new_password"/></li>
                        <li>Confirm Password: <input type="text" class="form-control" value="" name="confirm_password"/></li>
                        <li>Permissions Level: <select class="form-control" name="permissions">
                                    <option value="admin-permission">Admin</option>
                                    <option value="user-permission">User</option>
                                </select></li>
                    </ul>
                    <input type="submit" value="Save Changes" class="btn btn-primary"/>
                </form>
            </div>
            <?php include_once("includes/inc-footer.php"); ?>
        </body>
    </html>