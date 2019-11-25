<?php
    session_start();
    
    $user_id = $_SESSION["user_id"];
    $username = $_SESSION['username'];
    $permission_level = $_SESSION["permission_level"];
    if(!$user_id){
        //redirect to login
        header("Location: https://admin.authenticmerch.com");
    }
    error_reporting( E_ALL );
    ini_set( "display_errors", 1 );
    include_once("includes/inc-dbc-conn.php");
?>
    
<!doctype html>
<html lang='en'>
    <head>
        <title>Notification Manager - RCHQ Admin Area</title>
        <meta name="robots" content="noindex,nofollow"/>
        <?php
            include_once("includes/inc-html-header.php");
        ?>
    </head>
    <body>
        <?php 
            $title="Notifications";
            include_once("includes/inc-header.php"); 
        ?>
       
        <div class="container-fluid skip-nav">
        <h2>Notification Manager</h2>
            <table class="table table-striped">
            <thead class="thead thead-dark">
            <tr>
                <th>Notification</th>
                <th>Product</th>
                <th>Links</th>
            </tr>
            </thead>
        <?php
            $notification_query = "SELECT * FROM `admin_user_notifications` WHERE destination_user=" . $user_id;
            $results = mysqli_query($conn, $notification_query);
            if(mysqli_num_rows($results) > 0){
                while($notification = mysqli_fetch_assoc($results)){
                ?>
                    <tr>
                        <td><?php echo $notification['notification_text']; ?></td>
                        <td><a href="https://admin.authenticmerch.com/edit_product.php?product_id=<?php echo $notification['product_id']; ?>">Go to Product</a></td>
                        <td><a href="delete_notification.php?notification_id=<?php echo $notification['_id']; ?>"><i class="fas fa-trash-alt"></i></td>
                    </tr>

                <?php
                }
            }
            else{
                ?>
                <tr>
                    <td colspan="3"><p class="alert alert-success">Looks like you are all good here...</p></td>
                </tr>

                <?php
            }
        ?>
        </table>
        <a href="delete_notification.php?notification_id=ALL&user_id=<?php echo $user_id;?>" class="btn btn-primary">Clear All Notifications</a>
        </div>
        <?php include_once("includes/inc-footer.php"); ?>
    </body>
</html>