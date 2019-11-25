<?php
    ob_start();
    session_start();
    
    $user_id = $_SESSION["user_id"];
    $username = $_SESSION['username'];
    $permission_level = $_SESSION["permission_level"];
    if(!$user_id || $permission_level > 0){
        //redirect to login
        header("Location: https://admin.authenticmerch.com/dashboard.php");
    }
    else{
        //add_activity($user_id,"Visited Dashboard");
    }
    //error_reporting( E_ALL );
    //ini_set( "display_errors", 1 );
    include_once("includes/inc-dbc-conn.php");
    
    ?>
    
<!doctype html>
<html lang='en'>
    <head>
        <title>Edit Users - RCHQ Admin Area</title>
        <meta name="robots" content="noindex,nofollow"/>
        <?php
            include_once("includes/inc-html-header.php");
        ?>
    </head>
    <body>
        <?php include_once("includes/inc-header.php"); ?>
        <div class="container skip-nav">
            <h1 style="padding-bottom: 20px;">Edit Users</h1>
            <div class="dashboard-block client-block">
            <script type="text/javascript">
                $(document).ready(function(){
                            $(".remove_user_link").click(function(){
                                var account_id = $(this).attr('data');
                                //exceptions

                                var urlData = "user_management.php?"
                                    + "account_id=" + account_id;

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
            </script>
                <table class="table">
                    <thead>
                        <th class="fancy-name">ID</th>
                        <th class="fancy-name">User</th>
                        <th class="fancy-name">Links</th>
                    </thead>
                    <tbody>
                        <?php
                            $user_query= "SELECT * FROM `admin_login_details`";
                            $user_results = mysqli_query($conn,$user_query);
                            
                            if(!empty($user_results)){
                                while($row=mysqli_fetch_assoc($user_results)){
                                    $account_name = $row['username'];
                                    $account_id = $row['_id'];
                                    ?>
                                    <tr>
                                        <td><?php echo $account_id; ?></td>
                                        <td><b class="fancy-name"><?php echo $account_name; ?></b></td>
                                        <td class="links-cell">
                                            <a style="cursor: pointer;" class="remove_user_link" onClick="$(this).parent().parent().remove();" data="<?php echo $account_id; ?>"><i class="fas fa-minus-circle"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            else{
                                ?>
                                <tr>
                                    <td colspan="4">
                                        <p class="alert alert-danger">No users found... This is not supposed to happen</p>
                                    </td>
                                </tr>
                                <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php include_once("includes/inc-footer.php"); ?>
    </body>
</html>